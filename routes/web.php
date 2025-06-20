<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Middleware\UserMiddleware;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Auth;



    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

  Route::get('/', function () {
    return view('auth.login');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/customer', function () {
                    return redirect()->route('customers.index');

    });



    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class);
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

    //Rute untuk PDF Downloader
    Route::get('/{transaction}/download-ph-pdf', [TransactionController::class, 'downloadPHPdf'])->name('transactions.download_ph_pdf');
    });
     Route::get('/{transaction}/download-invoice-pdf', [TransactionController::class, 'downloadInvoicePdf'])->name('transactions.download_invoice_pdf');

    //Rute untuk payment
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/{invoice}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('/{invoice}/send-reminder', [PaymentController::class, 'sendReminder'])->name('payments.send_reminder');
    });

    // Route for updating payment status (handled by TransactionController)
    Route::get('transactions/{transaction}/edit-payment-status', [TransactionController::class, 'editPaymentStatus'])->name('transactions.edit_payment_status');
    Route::post('transactions/{transaction}/update-payment-status', [TransactionController::class, 'updatePaymentStatus'])->name('transactions.update_payment_status');

Route::middleware(['auth', 'UserMiddleware'])->group(function () {

});

Route::middleware(['auth', 'AdminMiddleware'])->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

});



require __DIR__.'/auth.php';
