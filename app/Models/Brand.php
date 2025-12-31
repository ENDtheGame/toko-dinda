<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sales_name',
        'sales_phone',
        'category_id',
        'status'
    ];

    // INI YANG DICARI CONTROLLER: Nama fungsi harus 'category'
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // INI UNTUK PROTEKSI HAPUS: Nama fungsi harus 'products'
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
