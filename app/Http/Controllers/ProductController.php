<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Menggunakan Type Hinting untuk keamanan
   public function index()
{
    // Menggunakan with('category') agar loading lebih cepat (Eager Loading)
    $products = Product::with('category')->latest()->get();
    $categories = Category::all();
    $units = Unit::all(); // Ambil semua satuan untuk ditampilkan

    return view('products.index', compact('products', 'categories','units'));
}

    public function create()
{
    $categories = Category::all();
    $units = Unit::all(); // Ambil semua satuan untuk dipilih
    return view('products.create', compact('categories', 'units'));
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'category_id' => 'required|exists:categories,id',
        'unit_id' => 'required|exists:units,id', // Ganti dari 'unit_id' ke 'unit_id'
        'purchase_price' => 'required|numeric',
        'selling_price' => 'required|numeric',
        'stock' => 'required|numeric',
        'wholesale_min' => 'nullable|numeric',
        'wholesale_price' => 'nullable|numeric',
    ]);

    Product::create($request->all());

    return redirect()->route('products.index')->with('success', 'Produk berhasil disimpan!');
}

public function update(Request $request, Product $product)
{
    $request->validate([
        'name' => 'required',
        'category_id' => 'required|exists:categories,id',
        'unit_id' => 'required|exists:units,id', // Ganti dari 'unit_id' ke 'unit_id'
        'purchase_price' => 'required|numeric',
        'selling_price' => 'required|numeric',
        'stock' => 'required|numeric',
    ]);

    $product->update($request->all());

    return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
}

    // Menggunakan Route Model Binding (langsung panggil Product $product)
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }


    public function destroy(Product $product)
{
    $product->delete();
    return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
}
}
