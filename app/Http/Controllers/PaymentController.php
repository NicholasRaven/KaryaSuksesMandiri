<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Notifications\InvoicePaymentNotification; // Corrected typo (Inovice -> Invoice)
use App\Mail\InvoiceReminder;

class PaymentController extends Controller
{
    /**
     * Menampilkan daftar semua invoice yang perlu dibayar
     * dengan informasi jatuh tempo dan status pembayaran.
     * Ini akan menjadi halaman utama untuk menu "Pembayaran".
     */
    public function index(Request $request)
    {
        // if (Auth::check()) {
        //     Auth::user()->unreadNotifications()
        //                 ->where('type', 'App\\Notifications\\InvoicePaymentNotification')
        //                 ->get()
        //                 ->markAsRead();
        // }

        // Memuat invoice bersama dengan relasi transaksi dan pelanggan
        $query = Invoice::with(['transaction.customer']);

        // Fitur pencarian untuk nomor invoice atau nama pelanggan
        $search = $request->input('search');
        if ($search) {
            $query->where('invoice_number', 'like', '%' . $search . '%')
                  ->orWhereHas('transaction.customer', function ($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
        }

        // Filter status pembayaran
        $status = $request->input('status');
        if ($status && $status !== 'All') {
            $query->whereHas('transaction', function ($q) use ($status) {
                $q->where('payment_status', $status);
            });
        } else {
            // Default: tampilkan yang belum lunas (Belum Ada Invoice, Belum Bayar, Jatuh Tempo)
            $query->whereHas('transaction', function ($q) {
                $q->whereIn('payment_status', ['Belum Ada Invoice', 'Belum Bayar', 'Jatuh Tempo']);
            });
        }

        // Urutkan berdasarkan jatuh tempo terdekat
        $invoices = $query->orderBy('due_date', 'asc')->paginate(10)->appends($request->except('page'));

        // Logika untuk mengubah status menjadi 'Jatuh Tempo' secara otomatis
        // Ini dijalankan saat halaman diakses. Untuk proses background lebih baik pakai Laravel Scheduler.
        foreach ($invoices as $invoice) {
            // Pastikan invoice memiliki due_date dan statusnya 'Belum Bayar' sebelum diubah ke 'Jatuh Tempo'
            if ($invoice->due_date && $invoice->due_date < now()->toDateString() && $invoice->transaction && $invoice->transaction->payment_status == 'Belum Bayar') {
                $invoice->transaction->update(['payment_status' => 'Jatuh Tempo']);
                // Perbarui objek di memori juga agar tampilan langsung berubah
                $invoice->transaction->payment_status = 'Jatuh Tempo';
                Log::info("Invoice {$invoice->invoice_number} changed to 'Jatuh Tempo' status automatically.");
            }
        }

        // Daftar status pembayaran untuk filter dropdown
        $paymentStatuses = ['All', 'Belum Ada Invoice', 'Belum Bayar', 'Jatuh Tempo', 'Lunas'];

        return view('payments.index', compact('invoices', 'search', 'status', 'paymentStatuses'));
    }

    /**
     * Menampilkan detail pembayaran untuk invoice tertentu.
     * Ini akan mengarahkan ke form edit_payment_status yang sudah ada
     * di TransactionController, karena form itu mengedit data transaksi/invoice.
     */
    public function show(Invoice $invoice)
    {
        // Memuat semua relasi yang dibutuhkan oleh form `transactions.edit_payment_status`
        $transaction = $invoice->transaction->load([
            'customer',
            'details.item',
            'details.selectedSupplierPrice.supplier',
            'invoice' // Pastikan invoice dimuat juga
        ]);

        if (!$transaction) {
            return redirect()->route('payments.index')->with('error', 'Transaksi tidak ditemukan untuk invoice ini.');
        }

        // Mengarahkan ke view yang sama dengan edit_payment_status di TransactionController
        // Karena view tersebut memang untuk mengupdate status pembayaran invoice
        return view('transactions.edit_payment_status', compact('transaction'));
    }

    /**
     * Mengirim reminder pembayaran untuk invoice.
     * Metode ini bisa dipanggil manual dari daftar pembayaran.
     */
    public function sendReminder(Invoice $invoice)
    {
        // Pastikan ada transaksi dan statusnya belum lunas
        if (!$invoice->transaction || $invoice->transaction->payment_status == 'Lunas') {
            return back()->with('error', 'Reminder tidak bisa dikirim: Invoice sudah lunas atau transaksi tidak valid.');
        }

        // Pastikan memiliki jatuh tempo untuk reminder
        if (!$invoice->due_date) {
            return back()->with('error', 'Reminder tidak bisa dikirim: Invoice tidak memiliki tanggal jatuh tempo.');
        }

        try {
            $customerEmail = $invoice->transaction->customer->email ?? null;
            $customerName = $invoice->transaction->customer->name ?? 'Pelanggan';

            if ($customerEmail) {
                // UNCOMMENT DAN KONFIGURASI BAGIAN INI JIKA ANDA INGIN MENGIRIM EMAIL ASLI
                // Pastikan Anda sudah mengkonfigurasi Mail di .env dan membuat Mailable class (php artisan make:mail InvoiceReminder)
                Mail::to($customerEmail)->send(new InvoiceReminder($invoice, $customerName));

                // Untuk sementara, kita hanya simulasi dan mencatat ke log/sesi
                Log::info("Reminder pembayaran dikirim untuk Invoice: {$invoice->invoice_number} ke {$customerEmail}");
                $message = 'Reminder pembayaran berhasil dikirim ke ' . $customerEmail;
            } else {
                Log::warning("Gagal mengirim reminder untuk Invoice {$invoice->invoice_number}: Email pelanggan tidak ditemukan.");
                $message = 'Gagal mengirim reminder: Email pelanggan tidak ditemukan.';
            }

            // Update timestamp reminder terakhir dikirim di model Invoice
            $invoice->update(['reminder_sent_at' => now()]);

            return back()->with('success', $message);

        } catch (\Exception $e) {
            // Log error jika pengiriman gagal
            Log::error("Gagal mengirim reminder untuk Invoice {$invoice->invoice_number}: " . $e->getMessage());
            return back()->with('error', 'Gagal mengirim reminder: ' . $e->getMessage());
        }
    }
}