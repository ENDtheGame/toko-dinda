<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Category;
use App\Models\Unit;

class Product extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'unit_id',         // Sesuaikan dengan nama di database (id satuan)
        'purchase_price',
        'selling_price',
        'stock',
        'wholesale_min',   // Tambahkan ini agar bisa simpan harga grosir
        'wholesale_price', // Tambahkan ini agar bisa simpan harga grosir
    ];
    public function products()
    {
    return $this->hasMany(Product::class);
}

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Tambahkan relasi ini agar bisa menampilkan nama satuan di tabel
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
