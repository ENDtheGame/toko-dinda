<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Tambahkan baris ini. Tanpa ini, tambah kategori akan selalu error merah.
    protected $fillable = ['name', 'parent_id'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Untuk memanggil siapa induknya
public function parent()
{
    return $this->belongsTo(Category::class, 'parent_id');
}

// Untuk memanggil siapa saja anaknya (sub-kategori)
public function children()
{
    return $this->hasMany(Category::class, 'parent_id');
}
}
