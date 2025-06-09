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
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
