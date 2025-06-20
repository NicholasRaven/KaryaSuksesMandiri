<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'po_file_path',
        'payment_received_date',
        'payment_method',
        'payment_proof_file',
        'reminder_sent_at'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
