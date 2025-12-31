<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    // Tambahkan baris ini
    protected $fillable = [
        'product_id',
        'quantity_added',
        'actual_purchase_price',
        'supplier'
    ];

    // Relasi ke produk (opsional tapi disarankan)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
