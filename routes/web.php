<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PaymentController; // Penting: Tambahkan ini

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/customer', function () {
        return redirect()->route('customers.index');
    });

    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class);

    // Grup rute untuk Transaksi dengan prefix dan namespace 'transactions.'
    Route::prefix('transactions')->name('transactions.')->group(function () {
        // Rute standar CRUD Transaction
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::get('/{transaction}', [TransactionController::class, 'show'])->name('show');
        // Contoh rute edit/update/destroy (jika Route::resource tidak dipakai penuh)
        // Route::get('/{transaction}/edit', [TransactionController::class, 'edit'])->name('edit');
        // Route::put('/{transaction}', [TransactionController::class, 'update'])->name('update');
        // Route::delete('/{transaction}', [TransactionController::class, 'destroy'])->name('destroy');

        // Rute untuk Alur Transaksi Bertahap
        Route::get('/{transaction}/input-prices', [TransactionController::class, 'inputSupplierPrices'])->name('input_supplier_prices');
        Route::post('/{transaction}/store-prices', [TransactionController::class, 'storeSupplierPrices'])->name('store_supplier_prices');

        Route::get('/{transaction}/generate-ph', [TransactionController::class, 'generatePH'])->name('generate_ph');
        Route::post('/{transaction}/confirm-ph-sent', [TransactionController::class, 'confirmPHSent'])->name('confirm_ph_sent');

        Route::get('/{transaction}/confirm-po-received', [TransactionController::class, 'confirmPOReceived'])->name('confirm_po_received');
        Route::post('/{transaction}/store-po-received', [TransactionController::class, 'storePOReceived'])->name('store_po_received');

        Route::get('/{transaction}/generate-invoice', [TransactionController::class, 'generateInvoice'])->name('generate_invoice');
        Route::post('/{transaction}/store-invoice', [TransactionController::class, 'storeInvoice'])->name('store_invoice');

        // Rute untuk Update Status Pembayaran (Form dan Submit)
        // Ini di TransactionController karena langsung memanipulasi data transaksi/invoice
        Route::get('/{transaction}/edit-payment-status', [TransactionController::class, 'editPaymentStatus'])->name('edit_payment_status');
        Route::put('/{transaction}/update-payment-status', [TransactionController::class, 'updatePaymentStatus'])->name('update_payment_status');

        // Rute spesifik mark as completed
        Route::put('/{transaction}/mark-as-completed', [TransactionController::class, 'markAsCompleted'])->name('mark_as_completed');

        // Rute untuk Download PDF
        Route::get('/{transaction}/download-ph-pdf', [TransactionController::class, 'downloadPHPdf'])->name('download_ph_pdf');
        Route::get('/{transaction}/download-invoice-pdf', [TransactionController::class, 'downloadInvoicePdf'])->name('download_invoice_pdf');
    });

    // Grup rute BARU untuk Sistem Pembayaran
    // Semua rute di sini akan memiliki prefix 'payments/' dan name 'payments.'
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index'); // Daftar semua invoice untuk pembayaran
        Route::get('/{invoice}', [PaymentController::class, 'show'])->name('show'); // Detail/Form update pembayaran per invoice
        Route::post('/{invoice}/send-reminder', [PaymentController::class, 'sendReminder'])->name('send_reminder'); // Mengirim reminder
    });
    Route::resource('transactions', TransactionController::class);

    //Rute untuk mengupdate status
    Route::post('transactions/create', [TransactionController::class, 'store'])->name('transactions.store');

    Route::post('transactions/{transaction}/status/{type}', [TransactionController::class, 'updateStatus'])->name('transactions.updateStatus');

    Route::get('transactions/{transaction}/input-prices', [TransactionController::class, 'inputSupplierPrices'])->name('transactions.input_supplier_prices'); // Step 2 GET
    Route::post('transactions/{transaction}/store-prices', [TransactionController::class, 'storeSupplierPrices'])->name('transactions.store_supplier_prices'); // Step 2 POST

    Route::get('transactions/{transaction}/generate-ph', [TransactionController::class, 'generatePH'])->name('transactions.generate_ph'); // Step 3 GET
    Route::post('transactions/{transaction}/confirm-ph-sent', [TransactionController::class, 'confirmPHSent'])->name('transactions.confirm_ph_sent'); // Step 3 POST (Tandai sebagai PH Dikirim)

    Route::get('transactions/{transaction}/confirm-po', [TransactionController::class, 'confirmPOReceived'])->name('transactions.confirm_po_received'); // Step 4 GET
    Route::post('transactions/{transaction}/store-po', [TransactionController::class, 'storePOReceived'])->name('transactions.store_po_received'); // Step 4 POST

    Route::get('transactions/{transaction}/create-invoice', [TransactionController::class, 'createInvoice'])->name('transactions.create_invoice'); // Step 5 GET
    Route::post('transactions/{transaction}/store-invoice', [TransactionController::class, 'storeInvoice'])->name('transactions.store_invoice'); // Step 5 POST
});

require __DIR__.'/auth.php';
