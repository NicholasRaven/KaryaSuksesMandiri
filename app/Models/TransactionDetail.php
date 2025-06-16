<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'item_id',
        'item_name', // Untuk kasus item yang diinput manual
        'quantity',
        'specification_notes',
        'final_price_per_unit', // Harga final yang dipilih dari supplier
    ];

    /**
     * Get the transaction that owns the TransactionDetail.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the item associated with the TransactionDetail.
     * This is for master item data, can be null if item_name is manual.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get all supplier prices for the transaction detail.
     */
    public function supplierPrices(): HasMany
    {
        return $this->hasMany(ItemSupplierPrice::class);
    }

    /**
     * Get the selected supplier price for the transaction detail.
     * This relationship assumes 'is_selected' column exists in ItemSupplierPrice model
     * and is used to mark the chosen price.
     */
    public function selectedSupplierPrice(): HasOne
    {
        return $this->hasOne(ItemSupplierPrice::class)->where('is_selected', true);
    }
}
