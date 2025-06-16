<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Item;
use App\Models\TransactionDetail;
use App\Models\Supplier;
use App\Models\ItemSupplierPrice;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF; // Import the PDF Facade
use Carbon\Carbon; // Pastikan ini di-use

class TransactionController extends Controller
{
    /**
     * 1. Dashboard Sistem Transaksi (Index)
     * Menampilkan daftar transaksi dengan fitur pencarian dan paginasi.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['customer', 'invoice', 'details']);

        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        if ($search) {
            $query->where('transaction_number', 'like', '%' . $search . '%')
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
        }

        $transactions = $query->orderByDesc('created_at')->paginate($perPage)->appends($request->except('page'));

        return view('transactions.index', compact('transactions', 'search', 'perPage'));
    }

    /**
     * 2. Tampilan Form Tambah Transaksi Baru
     * Menampilkan formulir untuk membuat transaksi baru, memilih pelanggan, dan menambahkan barang pesanan.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $items = Item::orderBy('name')->get();
        return view('transactions.create_step1', compact('customers', 'items'));
    }

    /**
     * 2. Simpan Transaksi Baru
     * Menyimpan data transaksi awal dan detail barang yang dipesan.
     * Setelah berhasil, mengarahkan ke langkah selanjutnya: input harga supplier.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'delivery_address' => 'nullable|string',
            'orderer_name' => 'nullable|string|max:255',
            'orderer_email' => 'nullable|email|max:255',
            'orderer_phone' => 'nullable|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.item_name' => 'required_without:items.*.item_id|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.specification' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $latestTransaction = Transaction::orderByDesc('id')->first();
            $nextId = ($latestTransaction) ? $latestTransaction->id + 1 : 1;
            $transactionNumber = 'TR-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $transaction = Transaction::create([
                'transaction_number' => $transactionNumber,
                'customer_id' => $request->customer_id,
                'order_date' => $request->order_date,
                'shipping_address' => $request->delivery_address,
                'process_status' => 'PO Diterima', // Status awal
                'payment_status' => 'Belum Ada Invoice', // Status pembayaran awal
                'total_price' => 0,
            ]);

            foreach ($request->items as $itemData) {
                $itemId = $itemData['item_id'] ?? null;
                $itemName = $itemData['item_name'];

                if ($itemId) {
                    $masterItem = Item::find($itemId);
                    if ($masterItem) {
                        $itemName = $masterItem->name;
                    }
                }

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $itemId,
                    'item_name' => $itemName,
                    'quantity' => $itemData['quantity'],
                    'specification_notes' => $itemData['specification'],
                ]);
            }

            DB::commit();
            return redirect()->route('transactions.input_supplier_prices', $transaction->id)
                             ->with('success', 'Transaksi awal berhasil dibuat. Lanjutkan untuk input harga supplier.');

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage()); // Uncomment for debugging
            return back()->withInput()->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
        }
    }

    /**
     * 3. Tampilan Form Input Harga Supplier
     * Menampilkan formulir untuk setiap barang pesanan agar bisa diinput harga dari berbagai supplier.
     */
    public function inputSupplierPrices(Transaction $transaction)
    {
        $transaction->load(['details.item', 'details.supplierPrices.supplier']);
        $suppliers = Supplier::orderBy('name')->get();

        return view('transactions.input_supplier_prices', compact('transaction', 'suppliers'));
    }

    /**
     * 3. Simpan Harga Supplier
     * Menyimpan harga penawaran dari supplier untuk setiap detail transaksi dan menandai yang dipilih.
     */
    public function storeSupplierPrices(Request $request, Transaction $transaction)
    {
        $request->validate([
            'transaction_details' => 'required|array',
            'transaction_details.*.id' => 'required|exists:transaction_details,id',
            'transaction_details.*.selected_price_id' => 'nullable|integer',
            'transaction_details.*.prices' => 'array',
            'transaction_details.*.prices.*.supplier_id' => 'required|exists:suppliers,id',
            'transaction_details.*.prices.*.price' => 'required|numeric|min:0',
            'transaction_details.*.prices.*.notes' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->transaction_details as $detailData) {
                $transactionDetail = TransactionDetail::findOrFail($detailData['id']);

                ItemSupplierPrice::where('transaction_detail_id', $transactionDetail->id)
                                 ->update(['is_selected' => false]);

                $selectedPricePerUnit = null;

                if (isset($detailData['prices']) && is_array($detailData['prices'])) {
                    foreach ($detailData['prices'] as $priceInput) {
                        $itemSupplierPrice = ItemSupplierPrice::updateOrCreate(
                            [
                                'transaction_detail_id' => $transactionDetail->id,
                                'supplier_id' => $priceInput['supplier_id'],
                            ],
                            [
                                'price' => $priceInput['price'],
                                'notes' => $priceInput['notes'],
                            ]
                        );

                        if (isset($detailData['selected_price_id']) && $itemSupplierPrice->id == $detailData['selected_price_id']) {
                            $itemSupplierPrice->update(['is_selected' => true]);
                            $selectedPricePerUnit = $itemSupplierPrice->price;
                        }
                    }
                }

                if (isset($detailData['selected_price_id']) && $selectedPricePerUnit === null) {
                    $existingSelectedPrice = ItemSupplierPrice::find($detailData['selected_price_id']);
                    if ($existingSelectedPrice && $existingSelectedPrice->transaction_detail_id == $transactionDetail->id) {
                        $existingSelectedPrice->update(['is_selected' => true]);
                        $selectedPricePerUnit = $existingSelectedPrice->price;
                    }
                }

                $transactionDetail->update([
                    'final_price_per_unit' => $selectedPricePerUnit,
                ]);
            }

            DB::commit();
            return redirect()->route('transactions.generate_ph', $transaction->id)
                             ->with('success', 'Harga supplier berhasil disimpan. Lanjutkan ke pembuatan Penawaran Harga.');

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage()); // Uncomment for debugging
            return back()->withInput()->with('error', 'Gagal menyimpan harga supplier: ' . $e->getMessage());
        }
    }


    /**
     * 4. Tampilan Penawaran Harga (PH)
     * Menampilkan rincian penawaran harga.
     */
    public function generatePH(Transaction $transaction)
    {
        $transaction->load(['customer', 'details.item', 'details.selectedSupplierPrice.supplier']);

        // Set status proses dan pembayaran saat PH dibuat/dilihat pertama kali
        if ($transaction->process_status == 'Harga Disepakati' || $transaction->process_status == 'PO Diterima') {
            $transaction->update([
                'process_status' => 'PH Dikirim',
                'payment_status' => 'Belum Ada Invoice', // Inisialisasi payment_status
            ]);
        }

        $phSubtotal = 0;
        foreach ($transaction->details as $detail) {
            if ($detail->selectedSupplierPrice) {
                $phSubtotal += $detail->selectedSupplierPrice->price * $detail->quantity;
            }
        }

        return view('transactions.generate_ph', compact('transaction', 'phSubtotal'));
    }

    /**
     * 4. Aksi Konfirmasi PH Dikirim
     * Mengupdate status proses transaksi menjadi 'PH Dikirim'.
     */
    public function confirmPHSent(Request $request, Transaction $transaction)
    {
        // Anda bisa menambahkan validasi $request->ph_notes jika ingin catatan wajib
        if ($request->has('ph_notes')) {
            $transaction->ph_notes = $request->ph_notes;
        }

        // Pastikan status proses adalah 'PH Dikirim' dan payment_status diinisialisasi
        $transaction->process_status = 'PH Dikirim';
        $transaction->payment_status = 'Belum Ada Invoice'; // Penting: Tetapkan status pembayaran awal
        $transaction->save();

        return redirect()->route('transactions.confirm_po_received', $transaction->id)
                         ->with('success', 'Penawaran Harga berhasil ditandai sebagai PH Dikirim. Menunggu Konfirmasi PO.');
    }

    /**
     * 5. Tampilan Konfirmasi PO Diterima
     * Menampilkan form untuk mengunggah file PO yang diterima dari pelanggan.
     */
    public function confirmPOReceived(Transaction $transaction)
    {
        $transaction->load('customer', 'invoice');
        return view('transactions.confirm_po_received', compact('transaction'));
    }

    /**
     * 5. Simpan Konfirmasi PO Diterima
     * Mengunggah file PO dan menyimpan path-nya di tabel `invoices`.
     * Mengupdate status proses transaksi.
     */
    public function storePOReceived(Request $request, Transaction $transaction)
    {
        $request->validate([
            'po_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $invoice = $transaction->invoice;
            // Jika invoice belum ada, buat draft invoice
            if (!$invoice) {
                $invoice = Invoice::create([
                    'transaction_id' => $transaction->id,
                    'invoice_number' => 'DRAFT-INV-' . $transaction->transaction_number,
                    'invoice_date' => now()->toDateString(),
                    'due_date' => now()->addDays(7)->toDateString(), // Contoh: jatuh tempo 7 hari dari sekarang
                    'subtotal' => 0, 'tax_percentage' => 0, 'other_costs' => 0, 'total_amount' => 0
                ]);
            }

            if ($request->hasFile('po_file')) {
                // Hapus file lama jika ada
                if ($invoice->po_file && Storage::exists(str_replace('storage/', 'public/', $invoice->po_file))) {
                    Storage::delete(str_replace('storage/', 'public/', $invoice->po_file));
                }
                $filePath = $request->file('po_file')->store('public/po_files');
                $invoice->po_file = str_replace('public/', 'storage/', $filePath); // Pastikan path yang disimpan adalah path publik
                $invoice->save();
            }

            // Update status transaksi menjadi PO Dikonfirmasi
            $transaction->update([
                'process_status' => 'PO Dikonfirmasi',
                // payment_status tetap 'Belum Ada Invoice' sampai invoice dibuat
            ]);


            DB::commit();
            return redirect()->route('transactions.generate_invoice', $transaction->id)
                             ->with('success', 'Konfirmasi PO diterima dan file berhasil diunggah. Lanjutkan untuk membuat Invoice.');

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage()); // Uncomment for debugging
            return back()->withInput()->with('error', 'Gagal mengunggah file PO: ' . $e->getMessage());
        }
    }


    /**
     * 6. Tampilan Form Invoice
     * Menampilkan form untuk membuat atau mengedit invoice, dengan perhitungan subtotal, pajak, dan total.
     */
    public function generateInvoice(Transaction $transaction)
    {
        $transaction->load(['details.item', 'details.selectedSupplierPrice.supplier', 'invoice']);

        // Jika invoice sudah ada, arahkan ke halaman edit payment status
        if ($transaction->invoice) {
            return redirect()->route('transactions.edit_payment_status', $transaction->id)
                             ->with('info', 'Invoice sudah dibuat. Anda diarahkan ke halaman update pembayaran.');
        }

        $subtotal = 0;
        foreach ($transaction->details as $detail) {
            if ($detail->selectedSupplierPrice) {
                $subtotal += $detail->selectedSupplierPrice->price * $detail->quantity;
            }
        }

        // Nilai default untuk form
        $defaultTaxPercentage = 11; // Contoh PPN 11%
        $defaultOtherCosts = 0;

        return view('transactions.generate_invoice', compact('transaction', 'subtotal', 'defaultTaxPercentage', 'defaultOtherCosts'));
    }

    /**
     * 6. Simpan Invoice
     * Menyimpan data invoice ke database dan memperbarui status transaksi.
     */
    public function storeInvoice(Request $request, Transaction $transaction)
    {
        $request->validate([
            // Validasi invoice_number unik per transaksi atau secara global jika dibutuhkan
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number,' . ($transaction->invoice ? $transaction->invoice->id : 'NULL') . ',id,transaction_id,' . $transaction->id,
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'tax_percentage' => 'nullable|numeric|min:0',
            'other_costs' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = $transaction->details->sum(function ($detail) {
                return ($detail->final_price_per_unit ?? 0) * $detail->quantity;
            });

            $taxPercentage = $request->input('tax_percentage', 0);
            $otherCosts = $request->input('other_costs', 0);

            $taxAmount = ($taxPercentage / 100) * $subtotal;
            $totalAmount = $subtotal + $taxAmount + $otherCosts;

            // Update atau Buat Invoice baru
            $invoice = Invoice::updateOrCreate(
                ['transaction_id' => $transaction->id], // Cari berdasarkan transaction_id
                [
                    'invoice_number' => $request->invoice_number,
                    'invoice_date' => $request->invoice_date,
                    'due_date' => $request->due_date,
                    'subtotal' => $subtotal,
                    'tax_percentage' => $taxPercentage,
                    'other_costs' => $otherCosts,
                    'total_amount' => $totalAmount,
                    // po_file akan tetap dari yang sudah diupload sebelumnya di storePOReceived
                    // payment_received_date, payment_method, payment_proof_file, reminder_sent_at akan nullable
                ]
            );

            // Update status transaksi setelah invoice dibuat
            $transaction->update([
                'process_status' => 'Invoice Dibuat',
                'payment_status' => 'Belum Bayar', // Set status pembayaran menjadi "Belum Bayar"
                'total_price' => $totalAmount,
            ]);

            DB::commit();
            // Arahkan ke halaman detail transaksi, atau bisa juga ke daftar pembayaran
            return redirect()->route('transactions.show', $transaction->id)->with('success', 'Invoice berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage()); // Uncomment for debugging
            return back()->withInput()->with('error', 'Gagal menyimpan invoice: ' . $e->getMessage());
        }
    }

    /**
     * Aksi untuk melihat detail transaksi (view button)
     * Ini adalah metode `show()` yang harus ada di controller ini.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['customer', 'details.item', 'details.supplierPrices.supplier', 'invoice']);
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Menampilkan form untuk update status pembayaran invoice.
     * Ini adalah form yang sama yang akan diakses dari PaymentController::show.
     */
    public function editPaymentStatus(Transaction $transaction)
    {
        $transaction->load(['invoice', 'customer', 'details.item', 'details.selectedSupplierPrice.supplier']);

        if (!$transaction->invoice) {
            return back()->with('error', 'Invoice belum tersedia untuk transaksi ini.');
        }

        return view('transactions.edit_payment_status', compact('transaction'));
    }

    /**
     * Mengupdate status pembayaran invoice dan detail pembayaran.
     */
    public function updatePaymentStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'payment_status' => 'required|string|in:Belum Bayar,Jatuh Tempo,Lunas',
            'payment_received_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:255',
            'payment_proof_file' => 'nullable|file|mimes:jpeg,png,pdf|max:2048', // Max 2MB
        ]);

        if (!$transaction->invoice) {
            return back()->with('error', 'Invoice tidak ditemukan.');
        }

        DB::beginTransaction(); // Memulai transaksi database
        try {
            $invoice = $transaction->invoice;

            // Handle file upload untuk bukti pembayaran
            $paymentProofFilePath = $invoice->payment_proof_file; // Pertahankan file lama jika tidak ada upload baru
            if ($request->hasFile('payment_proof_file')) {
                // Hapus file lama jika ada
                if ($paymentProofFilePath && Storage::exists(str_replace('storage/', 'public/', $paymentProofFilePath))) {
                    Storage::delete(str_replace('storage/', 'public/', $paymentProofFilePath));
                }
                $filePath = $request->file('payment_proof_file')->store('public/payment_proofs');
                $paymentProofFilePath = str_replace('public/', 'storage/', $filePath); // Ubah ke path yang bisa diakses publik
            }

            // Update detail pembayaran di invoice
            $invoice->update([
                'payment_received_date' => $request->payment_received_date,
                'payment_method' => $request->payment_method,
                'payment_proof_file' => $paymentProofFilePath,
                // reminder_sent_at tidak diupdate di sini, hanya saat reminder dikirim
            ]);

            // Update payment_status di transaksi
            $transaction->update([
                'payment_status' => $request->payment_status,
            ]);

            DB::commit(); // Commit transaksi database
            return redirect()->route('transactions.show', $transaction->id)->with('success', 'Status pembayaran dan detail invoice berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika ada error
            // dd($e->getMessage()); // Debugging
            return back()->withInput()->with('error', 'Gagal memperbarui status pembayaran: ' . $e->getMessage());
        }
    }


    /**
     * Menandai transaksi sebagai "Selesai" (misal: pengiriman selesai).
     * Biasanya di dashboard atau di detail transaksi.
     */
    public function markAsCompleted(Transaction $transaction)
    {
        if ($transaction->payment_status !== 'Lunas') {
            return back()->with('error', 'Transaksi hanya bisa diselesaikan jika status pembayaran sudah "Lunas".');
        }

        $transaction->update(['process_status' => 'Selesai']);

        return back()->with('success', 'Transaksi berhasil ditandai sebagai Selesai.');
    }

    /**
     * Unduh Penawaran Harga (PH) sebagai PDF.
     */
    public function downloadPHPdf(Transaction $transaction)
    {
        $transaction->load(['customer', 'details.item', 'details.selectedSupplierPrice.supplier']);

        $phSubtotal = 0;
        foreach ($transaction->details as $detail) {
            if ($detail->selectedSupplierPrice) {
                $phSubtotal += $detail->selectedSupplierPrice->price * $detail->quantity;
            }
        }

        $data = [
            'transaction' => $transaction,
            'phSubtotal' => $phSubtotal,
            // Tambahkan data lain yang diperlukan di PDF
        ];

        // Memuat view Blade khusus untuk PDF PH dari folder 'pdfs'
        $pdf = FacadePdf::loadView('pdf.penawaran_harga', $data);

        // Unduh file PDF dengan nama yang sesuai
        return $pdf->download('Penawaran_Harga_' . $transaction->transaction_number . '.pdf');
    }

    /**
     * Unduh Invoice sebagai PDF.
     */
    public function downloadInvoicePdf(Transaction $transaction)
    {
        // Pastikan invoice dan relasi lainnya dimuat
        $transaction->load(['customer', 'details.item', 'details.selectedSupplierPrice.supplier', 'invoice']);

        if (!$transaction->invoice) {
            return back()->with('error', 'Invoice belum tersedia untuk transaksi ini.');
        }

        $invoice = $transaction->invoice;
        $subtotal = $invoice->subtotal;
        $taxAmount = ($invoice->tax_percentage / 100) * $subtotal;
        $totalAmount = $invoice->total_amount;

        $data = [
            'transaction' => $transaction,
            'invoice' => $invoice,
            'subtotal' => $subtotal,
            'taxAmount' => $taxAmount,
            'totalAmount' => $totalAmount,
        ];

        // Memuat view Blade khusus untuk PDF Invoice dari folder 'pdfs'
        $pdf = FacadePdf::loadView('pdf.invoice', $data);

        // Unduh file PDF dengan nama yang sesuai
        return $pdf->download('Invoice_' . $invoice->invoice_number . '.pdf');
    }
}
