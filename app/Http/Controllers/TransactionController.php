<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Item;
use App\Models\TransactionDetail;
use App\Models\Supplier;
use App\Models\ItemSupplierPrice;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF; // Import the PDF Facade

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
<<<<<<< Updated upstream
            'shipping_address' => 'nullable|string',
            'orderer_name' => 'nullable|string|max:255',
            'orderer_email' => 'nullable|email|max:255',
            'orderer_phone' => 'nullable|string|max:20',
            'items' => 'required|array|min:1', // Pastikan ada minimal 1 barang
            'items.*.item_id' => 'nullable|exists:items,id', // item_id bisa null jika input manual
            'items.*.item_name' => 'required_without:items.*.item_id|string|max:255', // Nama barang required jika item_id null
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.specification_notes' => 'nullable|string',
=======
            'delivery_address' => 'nullable|string',
            'orderer_name' => 'nullable|string|max:255',
            'orderer_email' => 'nullable|email|max:255',
            'orderer_phone' => 'nullable|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.item_name' => 'required_without:items.*.item_id|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.specification' => 'nullable|string',
>>>>>>> Stashed changes
        ]);

        DB::beginTransaction();
        try {
<<<<<<< Updated upstream
            // Generate nomor transaksi (contoh: TR-20240708-0001)
=======
>>>>>>> Stashed changes
            $latestTransaction = Transaction::orderByDesc('id')->first();
            $nextId = ($latestTransaction) ? $latestTransaction->id + 1 : 1;
            $transactionNumber = 'TR-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $transaction = Transaction::create([
                'transaction_number' => $transactionNumber,
                'customer_id' => $request->customer_id,
                'order_date' => $request->order_date,
<<<<<<< Updated upstream
                'shipping_address' => $request->shipping_address,
                // `ph_notes` dan `total_price` akan diperbarui di langkah selanjutnya atau di-set default kosong di sini
                // Jika ingin `ph_notes` di-input di sini, tambahkan ke validasi dan field
                'process_status' => 'PO Diterima', // Status awal [cite: image_30e534.png, image_6c0191.png]
                'payment_status' => 'Belum Ada Invoice', // Status awal [cite: image_30e534.png, image_6c0191.png]
                'total_price' => 0, // Harga awal 0, akan diupdate saat invoice dibuat
                // `po_file` tidak ada di tabel `transactions` karena dipindahkan ke `invoices`
            ]);

            foreach ($request->items as $itemData) {
                // Gunakan item_id jika dipilih, jika tidak, gunakan item_name yang diinput manual
                $itemId = $itemData['item_id'] ?? null;
                $itemName = $itemData['item_name']; // Ini akan diambil dari select option text atau input manual

                // Jika item_id dipilih, pastikan item_name sesuai dengan master item
=======
                'shipping_address' => $request->delivery_address,
                'process_status' => 'PO Diterima',
                'payment_status' => 'Belum Ada Invoice',
                'total_price' => 0,
            ]);

            foreach ($request->items as $itemData) {
                $itemId = $itemData['item_id'] ?? null;
                $itemName = $itemData['item_name'];

>>>>>>> Stashed changes
                if ($itemId) {
                    $masterItem = Item::find($itemId);
                    if ($masterItem) {
                        $itemName = $masterItem->name;
                    }
                }

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $itemId,
<<<<<<< Updated upstream
                    'item_name' => $itemName, // Simpan juga nama barangnya
                    'quantity' => $itemData['quantity'],
                    'specification_notes' => $itemData['specification_notes'],
=======
                    'item_name' => $itemName,
                    'quantity' => $itemData['quantity'],
                    'specification_notes' => $itemData['specification'],
>>>>>>> Stashed changes
                ]);
            }

            DB::commit();
            return redirect()->route('transactions.input_supplier_prices', $transaction->id)
                             ->with('success', 'Transaksi awal berhasil dibuat. Lanjutkan untuk input harga supplier.');

        } catch (\Exception $e) {
            DB::rollBack();
<<<<<<< Updated upstream
=======
            // dd($e->getMessage()); // Uncomment for debugging
>>>>>>> Stashed changes
            return back()->withInput()->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
        }
    }

    /**
     * 3. Tampilan Form Input Harga Supplier
     * Menampilkan formulir untuk setiap barang pesanan agar bisa diinput harga dari berbagai supplier.
     */
    public function inputSupplierPrices(Transaction $transaction)
    {
<<<<<<< Updated upstream
        $transaction->load(['details.item', 'details.supplierPrices.supplier']); // Load detail transaksi, item, dan harga supplier yang sudah ada
        $suppliers = Supplier::orderBy('name')->get(); // Ambil semua supplier untuk dropdown
=======
        $transaction->load(['details.item', 'details.supplierPrices.supplier']);
        $suppliers = Supplier::orderBy('name')->get();
>>>>>>> Stashed changes

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
<<<<<<< Updated upstream
            'transaction_details.*.selected_price_id' => 'nullable|integer', // ID dari ItemSupplierPrice yang dipilih
            'transaction_details.*.prices' => 'array', // Array untuk harga-harga supplier yang diinput
=======
            'transaction_details.*.selected_price_id' => 'nullable|integer',
            'transaction_details.*.prices' => 'array',
>>>>>>> Stashed changes
            'transaction_details.*.prices.*.supplier_id' => 'required|exists:suppliers,id',
            'transaction_details.*.prices.*.price' => 'required|numeric|min:0',
            'transaction_details.*.prices.*.notes' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->transaction_details as $detailData) {
                $transactionDetail = TransactionDetail::findOrFail($detailData['id']);

<<<<<<< Updated upstream
                // Hapus semua pilihan sebelumnya untuk detail ini
                ItemSupplierPrice::where('transaction_detail_id', $transactionDetail->id)
                                 ->update(['is_selected' => false]);

                $selectedPricePerUnit = null; // Untuk menyimpan harga final yang dipilih

                // Simpan atau update harga supplier yang baru diinput/diedit
=======
                ItemSupplierPrice::where('transaction_detail_id', $transactionDetail->id)
                                 ->update(['is_selected' => false]);

                $selectedPricePerUnit = null;

>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
                        // Tandai harga yang dipilih dan ambil harganya
=======
>>>>>>> Stashed changes
                        if (isset($detailData['selected_price_id']) && $itemSupplierPrice->id == $detailData['selected_price_id']) {
                            $itemSupplierPrice->update(['is_selected' => true]);
                            $selectedPricePerUnit = $itemSupplierPrice->price;
                        }
                    }
                }

<<<<<<< Updated upstream
                // Jika harga yang dipilih bukan dari input baru (misal: harga lama yang dipilih)
                // Atau jika `selected_price_id` diberikan dan tidak ada di `prices` yang baru disimpan
=======
>>>>>>> Stashed changes
                if (isset($detailData['selected_price_id']) && $selectedPricePerUnit === null) {
                    $existingSelectedPrice = ItemSupplierPrice::find($detailData['selected_price_id']);
                    if ($existingSelectedPrice && $existingSelectedPrice->transaction_detail_id == $transactionDetail->id) {
                        $existingSelectedPrice->update(['is_selected' => true]);
                        $selectedPricePerUnit = $existingSelectedPrice->price;
                    }
                }

<<<<<<< Updated upstream
                // Update final_price_per_unit di TransactionDetail
=======
>>>>>>> Stashed changes
                $transactionDetail->update([
                    'final_price_per_unit' => $selectedPricePerUnit,
                ]);
            }

            DB::commit();
            return redirect()->route('transactions.generate_ph', $transaction->id)
                             ->with('success', 'Harga supplier berhasil disimpan. Lanjutkan ke pembuatan Penawaran Harga.');

        } catch (\Exception $e) {
            DB::rollBack();
<<<<<<< Updated upstream
=======
            // dd($e->getMessage()); // Uncomment for debugging
>>>>>>> Stashed changes
            return back()->withInput()->with('error', 'Gagal menyimpan harga supplier: ' . $e->getMessage());
        }
    }


    /**
     * 4. Tampilan Penawaran Harga (PH)
     * Menampilkan rincian penawaran harga.
     */
    public function generatePH(Transaction $transaction)
    {
<<<<<<< Updated upstream
        $transaction->load(['customer', 'details.item', 'details.selectedSupplierPrice.supplier']); // Eager load data penting
=======
        $transaction->load(['customer', 'details.item', 'details.selectedSupplierPrice.supplier']);
>>>>>>> Stashed changes

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
        if ($request->has('ph_notes')) {
            $transaction->ph_notes = $request->ph_notes;
        }

        $transaction->process_status = 'PH Dikirim';
        $transaction->save();

        return redirect()->route('transactions.confirm_po_received', $transaction->id)
                         ->with('success', 'Penawaran Harga berhasil ditandai sebagai PH Dikirim. Menunggu Konfirmasi PO.');
    }

    /**
     * 5. Tampilan Konfirmasi PO Diterima
     * Menampilkan form untuk mengunggah file PO yang diterima dari pelanggan.
<<<<<<< Updated upstream
     * 
=======
>>>>>>> Stashed changes
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
            if (!$invoice) {
                $invoice = Invoice::create([
                    'transaction_id' => $transaction->id,
                    'invoice_number' => 'DRAFT-INV-' . $transaction->transaction_number,
                    'invoice_date' => now()->toDateString(),
                    'subtotal' => 0, 'tax_percentage' => 0, 'other_costs' => 0, 'total_amount' => 0
                ]);
            }

            if ($request->hasFile('po_file')) {
                if ($invoice->po_file && Storage::disk('public')->exists($invoice->po_file)) {
                    Storage::disk('public')->delete($invoice->po_file);
                }
                $filePath = $request->file('po_file')->store('po_files', 'public');
                $invoice->po_file = $filePath;
                $invoice->save();
            }

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

        $subtotal = 0;
        foreach ($transaction->details as $detail) {
            if ($detail->selectedSupplierPrice) {
                $subtotal += $detail->selectedSupplierPrice->price * $detail->quantity;
            }
        }

        $invoice = $transaction->invoice;

        return view('transactions.generate_invoice', compact('transaction', 'subtotal', 'invoice'));
    }

    /**
     * 6. Simpan Invoice
     * Menyimpan data invoice ke database dan memperbarui status transaksi.
     */
    public function storeInvoice(Request $request, Transaction $transaction)
    {
        $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . ($transaction->invoice ? $transaction->invoice->id : 'NULL') . ',id,transaction_id,' . $transaction->id,
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'other_costs' => 'nullable|numeric|min:0',
            'subtotal_calculated' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = $request->input('subtotal_calculated');
            $taxPercentage = $request->input('tax_percentage', 0);
            $otherCosts = $request->input('other_costs', 0);

            $taxAmount = ($taxPercentage / 100) * $subtotal;
            $totalAmount = $subtotal + $taxAmount + $otherCosts;

            Invoice::updateOrCreate(
                ['transaction_id' => $transaction->id],
                [
                    'invoice_number' => $request->invoice_number,
                    'invoice_date' => $request->invoice_date,
                    'due_date' => $request->due_date,
                    'subtotal' => $subtotal,
                    'tax_percentage' => $taxPercentage,
                    'other_costs' => $otherCosts,
                    'total_amount' => $totalAmount,
                ]
            );

            $transaction->update([
                'process_status' => 'Invoice Dibuat',
                'payment_status' => 'Belum Bayar',
                'total_price' => $totalAmount,
            ]);

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Invoice berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage()); // Uncomment for debugging
            return back()->withInput()->with('error', 'Gagal menyimpan invoice: ' . $e->getMessage());
        }
    }

    /**
     * Aksi untuk melihat detail transaksi (view button)
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['customer', 'details.item', 'details.supplierPrices.supplier', 'invoice']);
        return view('transactions.show', compact('transaction'));
    }

    /**
     * 7. Update Status Pembayaran (dari Dashboard)
     * Mengupdate status pembayaran transaksi (misal: dari "Belum Bayar" menjadi "Lunas").
     */
    public function updatePaymentStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'payment_status' => 'required|in:Belum Bayar,Lunas',
        ]);

        $transaction->update(['payment_status' => $request->payment_status]);

        return back()->with('success', 'Status pembayaran berhasil diperbarui menjadi ' . $request->payment_status . '.');
    }

    /**
     * 8. Menandai Transaksi Selesai (dari Dashboard)
     * Mengupdate status proses transaksi menjadi "Selesai". Memerlukan status pembayaran "Lunas".
     */
    public function markAsCompleted(Transaction $transaction)
    {
        if ($transaction->payment_status !== 'Lunas') {
            return back()->with('error', 'Transaksi hanya bisa diselesaikan jika status pembayaran Lunas.');
        }

        $transaction->update(['process_status' => 'Selesai']);

        return back()->with('success', 'Transaksi berhasil ditandai sebagai Selesai.');
    }

<<<<<<< Updated upstream
}
=======
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

        // Memuat view Blade khusus untuk PDF PH
        $pdf = PDF::loadView('pdf.penawaran_harga', $data);

        // Unduh file PDF dengan nama yang sesuai
        return $pdf->download('[pdf.penawaran_harga' . $transaction->transaction_number . '.pdf');
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

        // Ambil data dari invoice yang sudah ada
        $invoice = $transaction->invoice;
        $subtotal = $invoice->subtotal;
        $taxAmount = ($invoice->tax_percentage / 100) * $subtotal; // Hitung ulang tax amount
        $totalAmount = $invoice->total_amount;

        $data = [
            'transaction' => $transaction,
            'invoice' => $invoice,
            'subtotal' => $subtotal,
            'taxAmount' => $taxAmount,
            'totalAmount' => $totalAmount,
        ];

        // Memuat view Blade khusus untuk PDF Invoice
        $pdf = PDF::loadView('pdf.invoice', $data);

        // Unduh file PDF dengan nama yang sesuai
        return $pdf->download('pdf.invoice' . $invoice->invoice_number . '.pdf');
    }
}
>>>>>>> Stashed changes
