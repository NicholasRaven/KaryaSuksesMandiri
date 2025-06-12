<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description'];

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function suppliers()
{
    return $this->belongsToMany(Supplier::class);
}
}
