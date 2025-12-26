<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    protected $fillable = ['sale_id', 'product_id', 'quantity', 'price', 'subtotal'];

    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault([
        'name' => 'Produk Tidak Ditemukan'
    ]);
    }
}
