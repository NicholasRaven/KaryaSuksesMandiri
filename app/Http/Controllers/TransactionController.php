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
use Illuminate\Support\Facades\Storage; // Untuk upload file

class TransactionController extends Controller
{
    /**
     * 1. Dashboard Sistem Transaksi (Index)
     * Menampilkan daftar transaksi dengan fitur pencarian dan paginasi.
     * [cite: image_30e534.png, image_6c0191.png]
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['customer', 'invoice', 'details']); // Eager load relasi yang dibutuhkan
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);
        // Fitur Cari Transaksi berdasarkan nomor transaksi atau nama pelanggan
        if ($search) { // Cek apakah $search memiliki nilai
            $query->where('transaction_number', 'like', '%' . $search . '%')
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
        }

        // Ambil data paginasi menggunakan $perPage
        // Gunakan appends($request->except('page')) agar parameter search/per_page tetap ada di link paginasi
        $transactions = $query->orderByDesc('created_at')->paginate($perPage)->appends($request->except('page'));

        return view('transactions.index', compact('transactions', 'search', 'perPage'));
    }

    /**
     * 2. Tampilan Form Tambah Transaksi Baru
     * Menampilkan formulir untuk membuat transaksi baru, memilih pelanggan, dan menambahkan barang pesanan.
     * [cite: image_30e515.png, image_6bfd54.png]
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get(); // Ambil semua pelanggan untuk dropdown
        $items = Item::orderBy('name')->get(); // Ambil semua item/barang untuk dropdown (jika pakai master barang)
        return view('transactions.create', compact('customers', 'items'));
    }

    /**
     * 2. Simpan Transaksi Baru
     * Menyimpan data transaksi awal dan detail barang yang dipesan.
     * Setelah berhasil, mengarahkan ke langkah selanjutnya: input harga supplier.
     * [cite: image_30e515.png, image_6bfd54.png]
     */
public function store(Request $request)
{
    $validatedData = $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'order_date' => 'required|date',
        'shipping_address' => 'nullable|string',
        'orderer_name' => 'nullable|string|max:255',
        'orderer_email' => 'nullable|email|max:255',
        'orderer_phone' => 'nullable|string|max:20',
        'items' => 'required|array|min:1',
        'items.*.item_id' => 'nullable|exists:items,id',
        'items.*.item_name' => 'required_without:items.*.item_id|string|max:255',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.specification_notes' => 'nullable|string',
    ]);

    DB::beginTransaction();
    try {
        // Generate transaction number
        $latestTransactionId = Transaction::max('id') ?? 0;
        $transactionNumber = 'TR-' . date('Ymd') . '-' . str_pad($latestTransactionId + 1, 4, '0', STR_PAD_LEFT);

        // Create Transaction
        $transaction = Transaction::create([
            'transaction_number' => $transactionNumber,
            'customer_id' => $validatedData['customer_id'],
            'order_date' => $validatedData['order_date'],
            'shipping_address' => $validatedData['shipping_address'],
            'process_status' => 'PO Diterima',
            'payment_status' => 'Belum Ada Invoice',
            'total_price' => 0,
        ]);

        // Insert Transaction Details
        foreach ($validatedData['items'] as $itemData) {
            $itemName = $itemData['item_name'];

            if (!empty($itemData['item_id'])) {
                $masterItem = Item::find($itemData['item_id']);
                if ($masterItem) {
                    $itemName = $masterItem->name;
                }
            }

            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'item_id' => $itemData['item_id'] ?? null,
                'item_name' => $itemName,
                'quantity' => $itemData['quantity'],
                'specification_notes' => $itemData['specification_notes'] ?? null,
            ]);
        }

        DB::commit();
        return redirect()->route('transactions.input_supplier_prices', $transaction->id)
                         ->with('success', 'Transaksi awal berhasil dibuat. Lanjutkan untuk input harga supplier.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
    }
}


    /**
     * 3. Tampilan Form Input Harga Supplier
     * Menampilkan formulir untuk setiap barang pesanan agar bisa diinput harga dari berbagai supplier.
     * [cite: image_30e26c.png, image_6bfaaa.png]
     */
public function inputSupplierPrices(Transaction $transaction)
{
    $transaction->load(['details.item', 'details.supplierPrices.supplier']);
    $suppliers = Supplier::orderBy('name')->get();

    // Find item names that have a matching supplier
    $itemNamesWithSuppliers = Supplier::pluck('name')->toArray();

    return view('transactions.input_supplier_prices', compact('transaction', 'suppliers', 'itemNamesWithSuppliers'));
}



    /**
     * 3. Simpan Harga Supplier
     * Menyimpan harga penawaran dari supplier untuk setiap detail transaksi dan menandai yang dipilih.
     * Setelah berhasil, mengarahkan ke langkah selanjutnya: generate PH.
     * [cite: image_30e26c.png, image_6bfaaa.png]
     */
public function storeSupplierPrices(Request $request, Transaction $transaction)
{
    $request->validate([
        'item_prices' => 'required|array',
        'item_prices.*' => 'required|array', // Each transaction_detail_id => array of prices
        'selected_prices' => 'nullable|array',
        'selected_prices.*' => 'nullable|integer|exists:item_supplier_prices,id',
    ]);

    DB::beginTransaction();
    try {
        foreach ($request->item_prices as $transactionDetailId => $supplierPrices) {
            $transactionDetail = TransactionDetail::findOrFail($transactionDetailId);

            // Reset selection for this detail
            ItemSupplierPrice::where('transaction_detail_id', $transactionDetailId)
                             ->update(['is_selected' => false]);

            $selectedPricePerUnit = null;

            foreach ($supplierPrices as $priceInput) {
                $itemSupplierPrice = ItemSupplierPrice::updateOrCreate(
                    [
                        'transaction_detail_id' => $transactionDetailId,
                        'supplier_id' => $priceInput['supplier_id'],
                    ],
                    [
                        'price' => $priceInput['price'],
                        'notes' => $priceInput['notes'] ?? null,
                    ]
                );

                // Check if this price is the selected one
                if (isset($request->selected_prices[$transactionDetailId]) &&
                    $itemSupplierPrice->id == $request->selected_prices[$transactionDetailId]) {
                    $itemSupplierPrice->update(['is_selected' => true]);
                    $selectedPricePerUnit = $itemSupplierPrice->price;
                }
            }

            // Handle selection of old prices
            if (isset($request->selected_prices[$transactionDetailId]) && $selectedPricePerUnit === null) {
                $existingSelectedPrice = ItemSupplierPrice::find($request->selected_prices[$transactionDetailId]);
                if ($existingSelectedPrice && $existingSelectedPrice->transaction_detail_id == $transactionDetailId) {
                    $existingSelectedPrice->update(['is_selected' => true]);
                    $selectedPricePerUnit = $existingSelectedPrice->price;
                }
            }

            // Update the final selected price
            $transactionDetail->update([
                'final_price_per_unit' => $selectedPricePerUnit,
            ]);
        }

        DB::commit();
        return redirect()->route('transactions.generate_ph', $transaction->id)
                         ->with('success', 'Harga supplier berhasil disimpan. Lanjutkan ke pembuatan Penawaran Harga.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', 'Gagal menyimpan harga supplier: ' . $e->getMessage());
    }
}



    /**
     * 4. Tampilan Penawaran Harga (PH)
     * Menampilkan rincian penawaran harga.
     * [cite: image_30e24d.png, image_6bfa8a.png]
     */
    public function generatePH(Transaction $transaction)
    {
        $transaction->load(['customer', 'details.item','details.selectedSupplierPrice.supplier']); // Remove selectedSupplierPrice

        // Hitung subtotal untuk PH
        $phSubtotal = 0;
        foreach ($transaction->details as $detail) {
            $phSubtotal += $detail->final_price_per_unit * $detail->quantity;
        }

        return view('transactions.generate_ph', compact('transaction', 'phSubtotal'));
    }


    /**
     * 4. Aksi Konfirmasi PH Dikirim
     * Mengupdate status proses transaksi menjadi 'PH Dikirim'.
     * [cite: image_30e24d.png, image_6bfa8a.png]
     */
    public function confirmPHSent(Request $request, Transaction $transaction)
    {
        // Jika ada catatan PH dari form, simpan ke kolom ph_notes di transaksi
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
     *
     */
    public function confirmPOReceived(Transaction $transaction)
    {
        $transaction->load('customer', 'invoice'); // Load invoice untuk cek apakah sudah ada file PO
        return view('transactions.confirm_po_received', compact('transaction'));
    }

    /**
     * 5. Simpan Konfirmasi PO Diterima
     * Mengunggah file PO dan menyimpan path-nya di tabel `invoices`.
     * Mengupdate status proses transaksi.
     * [cite: image_30e22d.png, image_6bfa54.png]
     */
    public function storePOReceived(Request $request, Transaction $transaction)
    {
        $request->validate([
            'po_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048', // Batasi tipe dan ukuran file
        ]);

        DB::beginTransaction();
        try {
            // Ambil atau buat instance Invoice (karena po_file ada di tabel invoices)
            $invoice = $transaction->invoice;
            if (!$invoice) {
                // Jika invoice belum ada, buat draft invoice untuk menyimpan file PO
                $invoice = Invoice::create([
                    'transaction_id' => $transaction->id,
                    'invoice_number' => 'DRAFT-INV-' . $transaction->transaction_number, // Nomor draft sementara
                    'invoice_date' => now()->toDateString(),
                    'subtotal' => 0, 'tax_percentage' => 0, 'other_costs' => 0, 'total_amount' => 0
                ]);
            }

            if ($request->hasFile('po_file')) {
                // Hapus file lama jika ada
                if ($invoice->po_file && Storage::disk('public')->exists($invoice->po_file)) {
                    Storage::disk('public')->delete($invoice->po_file);
                }
                $filePath = $request->file('po_file')->store('po_files', 'public'); // Simpan di storage/app/public/po_files
                $invoice->po_file = $filePath;
                $invoice->save();
            }

            // Setelah PO dikonfirmasi, transaksi siap untuk dibuat invoice.
            // Status proses mungkin tetap "PO Diterima", atau bisa diubah menjadi "PO Dikonfirmasi"
            // Untuk flow saat ini, biarkan 'PO Diterima' dan nanti 'Buat Invoice' yang akan mengubahnya.
            // Jika Anda ingin status khusus setelah PO dikonfirmasi, Anda bisa menambahkannya di sini.
            // Contoh: $transaction->process_status = 'PO Dikonfirmasi';
            // $transaction->save();


            DB::commit();
            return redirect()->route('transactions.create_invoice', $transaction->id)
                             ->with('success', 'Konfirmasi PO diterima dan file berhasil diunggah. Lanjutkan untuk membuat Invoice.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mengunggah file PO: ' . $e->getMessage());
        }
    }


    /**
     * 6. Tampilan Form Invoice
     * Menampilkan form untuk membuat atau mengedit invoice, dengan perhitungan subtotal, pajak, dan total.
     * [cite: image_30e20e.png, image_6bfa31.png]
     */
    public function createInvoice(Transaction $transaction)
    {
        $transaction->load(['details.item', 'details.selectedSupplierPrice.supplier', 'invoice']);

        $subtotal = 0;
        foreach ($transaction->details as $detail) {
            if ($detail->selectedSupplierPrice) {
                $subtotal += $detail->selectedSupplierPrice->price * $detail->quantity;
            }
        }

        $invoice = $transaction->invoice; // Ambil invoice yang sudah ada jika ada

        return view('transactions.generate_invoice', compact('transaction', 'subtotal', 'invoice'));
    }

    /**
     * 6. Simpan Invoice
     * Menyimpan data invoice ke database dan memperbarui status transaksi.
     * [cite: image_30e20e.png, image_6bfa31.png]
     */
        public function storeInvoice(Request $request, Transaction $transaction)
    {
        $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . ($transaction->invoice ? $transaction->invoice->id : 'NULL') . ',id,transaction_id,' . $transaction->id,
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'other_costs' => 'nullable|numeric|min:0',
            'subtotal_calculated' => 'required|numeric|min:0', // Hidden field dari frontend untuk keamanan
        ]);

        DB::beginTransaction();
        try {
            $subtotal = $request->input('subtotal_calculated'); // Ambil subtotal dari hidden input (sudah dihitung di frontend)
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
                    // po_file_path tidak diupdate di sini, sudah diupdate di storePOReceived
                ]
            );

            $transaction->update([
                'process_status' => 'Invoice Dibuat', // Update status proses [cite: image_30e534.png, image_6c0191.png]
                'payment_status' => 'Belum Bayar', // Update status pembayaran [cite: image_30e534.png, image_6c0191.png]
                'total_price' => $totalAmount, // Update total harga keseluruhan transaksi
            ]);

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Invoice berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan invoice: ' . $e->getMessage());
        }
    }

    /**
     * Aksi untuk melihat detail transaksi (view button)
     * [cite: image_30e534.png, image_6c0191.png]
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['customer', 'details.item', 'details.supplierPrices.supplier', 'invoice']); // Load semua relasi yang mungkin
        return view('transactions.show', compact('transaction'));
    }

    /**
     * 7. Update Status Pembayaran (dari Dashboard)
     * Mengupdate status pembayaran transaksi (misal: dari "Belum Bayar" menjadi "Lunas").
     * [cite: image_30e534.png, image_6c0191.png]
     */
    public function updatePaymentStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'payment_status' => 'required|in:Belum Bayar,Lunas', // Sesuaikan dengan status yang valid
        ]);

        $transaction->update(['payment_status' => $request->payment_status]); // Perubahan status pembayaran [cite: image_30e534.png, image_6c0191.png]

        return back()->with('success', 'Status pembayaran berhasil diperbarui menjadi ' . $request->payment_status . '.');
    }

    /**
     * 8. Menandai Transaksi Selesai (dari Dashboard)
     * Mengupdate status proses transaksi menjadi "Selesai". Memerlukan status pembayaran "Lunas".
     * [cite: image_30e534.png, image_6c0191.png]
     */
    public function markAsCompleted(Transaction $transaction)
    {
        // Hanya bisa diselesaikan jika pembayaran sudah lunas
        if ($transaction->payment_status !== 'Lunas') {
            return back()->with('error', 'Transaksi hanya bisa diselesaikan jika status pembayaran Lunas.');
        }

        $transaction->update(['process_status' => 'Selesai']); // Perubahan status proses [cite: image_30e534.png, image_6c0191.png]

        return back()->with('success', 'Transaksi berhasil ditandai sebagai Selesai.');
    }

}
