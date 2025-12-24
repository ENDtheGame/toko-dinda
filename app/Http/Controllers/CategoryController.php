<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
 public function index()
{
    // Semua kategori untuk dropdown di form (tetap ambil semua)
    $categories = Category::all();

    // Ambil Kategori Induk saja dengan pagination (misal 10 per halaman)
    $parentCategories = Category::whereNull('parent_id')
                        ->with('children')
                        ->paginate(3);

    return view('categories.index', compact('categories', 'parentCategories'));
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'parent_id' => 'nullable|exists:categories,id', // Validasi id induk harus ada di tabel categories
    ]);

    Category::create([
        'name' => $request->name,
        'parent_id' => $request->parent_id,
    ]);

    return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
}

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Kategori telah dihapus!');
    }

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'parent_id' => 'nullable|exists:categories,id',
    ]);

    $category = Category::findOrFail($id);

    // Cek biar dia gak milih dirinya sendiri sebagai bapaknya
    if ($request->parent_id == $id) {
        return back()->withErrors(['parent_id' => 'Kategori tidak bisa menjadi induk bagi dirinya sendiri.']);
    }

    $category->update([
        'name' => $request->name,
        'parent_id' => $request->parent_id,
    ]);

    return redirect()->back()->with('success', 'Perubahan kategori berhasil disimpan!');
}
}
