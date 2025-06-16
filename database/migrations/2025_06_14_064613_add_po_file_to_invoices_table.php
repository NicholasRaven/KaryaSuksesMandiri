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
        // Menambahkan kolom 'po_file' ke tabel 'invoices'
        Schema::table('invoices', function (Blueprint $table) {
            // Kolom string untuk menyimpan path file, bisa null
            // Ditempatkan setelah kolom 'total_amount' atau kolom lain yang relevan
            $table->string('po_file')->nullable()->after('total_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menghapus kolom 'po_file' jika migrasi di-rollback
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('po_file');
        });
    }
};
