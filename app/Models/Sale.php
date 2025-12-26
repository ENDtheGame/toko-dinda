<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'total_price',
        'pay_amount',
        'change_amount',
        'user_id',
    ];

    // Hapus $casts items karena kita pakai tabel sale_details

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        // Pastikan ini hasMany ke SaleDetail
        return $this->hasMany(SaleDetail::class);
    }
}
