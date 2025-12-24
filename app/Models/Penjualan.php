<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualan extends Model
{
    use HasFactory;
    // Nama tabel (opsional, Laravel default pakai 'penjualans')
    protected $table = 'penjualan';
    // Kolom yang bisa diisi
    protected $fillable = [ 'tanggal', 'kasir_id', 'total', ];
    // Relasi ke kasir (misalnya user)
    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }
}
