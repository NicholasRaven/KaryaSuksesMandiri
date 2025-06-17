<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pastikan tabel 'invoices' ada sebelum mencoba menambah kolom
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                // Tambahkan kolom untuk detail pembayaran dan reminder
                // 'after' adalah opsional, untuk mengatur posisi kolom
                $table->date('payment_received_date')->nullable()->after('total_amount');
                $table->string('payment_method')->nullable()->after('payment_received_date');
                $table->string('payment_proof_file')->nullable()->after('payment_method'); // Path ke file bukti pembayaran
                $table->timestamp('reminder_sent_at')->nullable()->after('due_date'); // Waktu terakhir reminder dikirim
            });
        }

        // Pastikan kolom payment_status ada di tabel transactions
        if (Schema::hasTable('transactions') && !Schema::hasColumn('transactions', 'payment_status')) {
            Schema::table('transactions', function (Blueprint $table) {
                // Tambahkan kolom payment_status ke tabel transactions
                // Default 'Belum Ada Invoice' jika invoice belum dibuat
                $table->string('payment_status')->default('Belum Ada Invoice')->after('process_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                // Hapus kolom jika rollback
                $table->dropColumn('payment_received_date');
                $table->dropColumn('payment_method');
                $table->dropColumn('payment_proof_file');
                $table->dropColumn('reminder_sent_at');
            });
        }

        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'payment_status')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('payment_status');
            });
        }
    }
};
