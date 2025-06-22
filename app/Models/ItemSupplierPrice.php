<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemSupplierPrice extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_detail_id',
        'item_id',
        'supplier_id',
        'price',
        'notes',
    ];

    public function transactionDetail()
    {
        return $this->belongsTo(TransactionDetail::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    
}
