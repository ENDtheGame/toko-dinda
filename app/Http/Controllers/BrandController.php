<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index() {
        $brands = Brand::latest()->get();
        return view('brands.index', compact('brands'));
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|unique:brands,name']);
        Brand::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);
        return back()->with('success', 'Brand berhasil ditambahkan');
    }

    public function destroy(Brand $brand)
{
    // Cek apakah ada produk yang menggunakan brand ini
    if ($brand->products()->count() > 0) {
        return back()->with('error', 'Brand tidak bisa dihapus karena masih digunakan oleh ' . $brand->products()->count() . ' produk!');
    }

    $brand->delete();
    return back()->with('success', 'Brand berhasil dihapus');
}
public function bulkDelete(Request $request)
{
    $ids = $request->ids; // Ambil array ID dari checkbox
    $canDelete = [];
    $cannotDelete = 0;

    foreach ($ids as $id) {
        $brand = Brand::find($id);
        if ($brand->products()->count() == 0) {
            $brand->delete();
            $canDelete[] = $id;
        } else {
            $cannotDelete++;
        }
    }

    if ($cannotDelete > 0) {
        return back()->with('success', count($canDelete) . ' Brand dihapus, tapi ' . $cannotDelete . ' Brand gagal dihapus karena masih ada produknya.');
    }

    return back()->with('success', 'Semua brand terpilih berhasil dihapus.');
}
}
