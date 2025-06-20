<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Carbon\Carbon; 

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik Transaksi
        $totalTransactions = Transaction::count();
        $transactionsInProgress = Transaction::whereIn('process_status', ['PO Diterima', 'Invoice Dibuat', 'PH Dikirim'])->count();
        $transactionsCompleted = Transaction::where('process_status', 'Selesai')->count();

        // Statistik Pembayaran Invoice
        $totalInvoices = Invoice::count();
        $invoicesUnpaid = Invoice::whereHas('transaction', function ($query) {
            $query->where('payment_status', 'Belum Bayar');
        })->count();
        $invoicesPaid = Invoice::whereHas('transaction', function ($query) {
            $query->where('payment_status', 'Lunas');
        })->count();

        // Total Omset dari Invoice Lunas
        $totalRevenue = Invoice::whereHas('transaction', function ($query) {
            $query->where('payment_status', 'Lunas');
        })->sum('total_amount');


        // Invoice Jatuh Tempo Mendekat (misal: dalam 7 hari)
        $upcomingDueInvoices = Invoice::whereNotNull('due_date')
                                    ->whereHas('transaction', function ($query) {
                                        $query->where('payment_status', '!=', 'Lunas');
                                    })
                                    ->where('due_date', '>=', Carbon::today())
                                    ->where('due_date', '<=', Carbon::today()->addDays(7))
                                    ->orderBy('due_date', 'asc')
                                    ->get();

        // Transaksi Terbaru (misal: 5 transaksi terakhir)
        $latestTransactions = Transaction::orderBy('created_at', 'desc')
                                        ->limit(5)
                                        ->with('customer') // Eager load customer untuk nama
                                        ->get();

        return view('dashboard', compact(
            'totalTransactions',
            'transactionsInProgress',
            'transactionsCompleted',
            'totalInvoices',
            'invoicesUnpaid',
            'invoicesPaid',
            'totalRevenue',
            'upcomingDueInvoices',
            'latestTransactions'
        ));
    }
}