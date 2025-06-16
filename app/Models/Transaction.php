<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'customer_id',
        'order_date',
        'process_status',
        'payment_status', // Pastikan ini ada di kolom database Anda
        'shipping_address',
        'orderer_name',
        'orderer_email',
        'orderer_phone',
        'total_price',
        'ph_notes',
    ];

    /**
     * Get the customer that owns the Transaction.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the transaction details for the transaction.
     */
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Get the invoice associated with the transaction.
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

}
