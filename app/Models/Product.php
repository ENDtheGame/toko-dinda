<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Brand;

class Product extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'category_id',
        'unit_id',
        'brand_id',
        'purchase_price',
        'selling_price',
        'stock',
        'min_stock',
        'wholesale_min',
        'wholesale_price',
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

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
