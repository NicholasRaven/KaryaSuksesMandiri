<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_percentage',
        'other_costs',
        'total_amount',
        'po_file',
        'payment_received_date', // Pastikan kolom ini ada di database Anda
        'payment_method',        // Pastikan kolom ini ada di database Anda
        'payment_proof_file',    // Pastikan kolom ini ada di database Anda
        'reminder_sent_at',      // Pastikan kolom ini ada di database Anda
    ];

    // Opsional: Untuk mengkonversi reminder_sent_at menjadi objek Carbon secara otomatis
    protected $dates = ['reminder_sent_at'];

    /**
     * Get the transaction that owns the Invoice.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
