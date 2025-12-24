<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    // Tambahkan baris ini
    protected $fillable = [
        'invoice_number',
        'total_price',
        'pay_amount',
        'change_amount',
        'user_id',
    ];

    // Relasi ke User (Kasir)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Detail Penjualan
    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }
}
