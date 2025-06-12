<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'jenis_barang',
        'phone_number',
        'email',
        'address',
    ];

    public function items(){
    return $this->hasMany(Item::class, 'item_name', 'name');
}
}
