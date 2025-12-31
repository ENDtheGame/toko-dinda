<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index(Request $request)
    {

        $search = $request->input('search');

        // Ambil semua data dari tabel kategori
        $main_categories = Category::orderBy('name', 'asc')->get();

        $brands = Brand::with(['category', 'products.category']) // Tambahkan products.category
            ->withCount('products')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        return view('brands.index', compact('brands', 'search', 'main_categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:brands,name',
            'category_id' => 'required|exists:categories,id', // Validasi ID harus ada di tabel categories
        ]);

        Brand::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sales_name' => $request->sales_name,
            'sales_phone' => $request->sales_phone,
            'category_id' => $request->category_id, // Gunakan ID
            'status' => 'active',
        ]);

        return back()->with('success', 'Brand berhasil disimpan');
    }

    public function update(Request $request, Brand $brand)
    {

        // Tambahkan validasi category_id agar data tetap konsisten
        $request->validate([
            'name' => 'required|unique:brands,name,' . $brand->id,
            'category_id' => 'required|exists:categories,id', // Validasi ini penting
        ]);

        $brand->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sales_name' => $request->sales_name,
            'sales_phone' => $request->sales_phone,
            'category_id' => $request->category_id,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Brand berhasil diperbarui');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->count() > 0) {
            return back()->with('error', 'Brand tidak bisa dihapus karena masih digunakan oleh ' . $brand->products()->count() . ' produk!');
        }

        $brand->delete();
        return back()->with('success', 'Brand berhasil dihapus');
    }

    // HANYA SATU FUNGSI bulkDelete DI SINI
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || empty($ids)) {
            return back()->with('error', 'Pilih minimal satu brand untuk dihapus.');
        }

        $canDeleteCount = 0;
        $cannotDeleteCount = 0;

        foreach ($ids as $id) {
            $brand = Brand::find($id);
            if ($brand && $brand->products()->count() == 0) {
                $brand->delete();
                $canDeleteCount++;
            } else {
                $cannotDeleteCount++;
            }
        }

        if ($cannotDeleteCount > 0) {
            return back()->with('success', $canDeleteCount . ' Brand dihapus, tapi ' . $cannotDeleteCount . ' Brand gagal dihapus karena masih ada produknya.');
        }

        return back()->with('success', 'Semua brand terpilih berhasil dihapus.');
    }
}
