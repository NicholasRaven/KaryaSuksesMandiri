<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_id',
        'item_id',
        'item_name',
        'quantity',
        'specification',
        'final_price_per_unit'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function supplierPrices()
    {
        return $this->hasMany(ItemSupplierPrice::class);
    }

        public function selectedSupplierPrice()
    {
        return $this->hasOne(ItemSupplierPrice::class)->where('is_selected', true);
    }
}
