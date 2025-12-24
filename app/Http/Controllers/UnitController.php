<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Product;


class UnitController extends Controller
{
    public function index()
{
    $units = Unit::all(); // Untuk dropdown
    $parentUnits = Unit::whereNull('parent_id')->with('children')->get(); // Untuk tabel accordion
    return view('units.index', compact('units', 'parentUnits'));
}
   public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'parent_id' => 'nullable|exists:units,id',
        'base_quantity' => 'nullable|numeric|min:0.01',
    ]);

    // Cek apakah user mencoba menjadikan satuan sebagai induk dirinya sendiri
    if ($request->parent_id && $request->name == Unit::find($request->parent_id)->name) {
        return redirect()->back()->with('error', 'Nama satuan tidak boleh sama dengan satuan dasarnya!');
    }

    Unit::create($request->all());

    return redirect()->route('units.index')->with('success', 'Satuan berhasil ditambahkan!');
}
    public function update(Request $request, Unit $unit)
    {
     $unit->update([
          'name' => $request->name,
          'parent_id' => $request->parent_id,
          'base_quantity' => $request->base_quantity ?? 1,
     ]);
     return redirect()->back()->with('success', 'Satuan berhasil diupdate!');
}
    public function destroy(Unit $unit)
{
    // Cek apakah ada produk yang pakai unit ini
    if ($unit->products()->count() > 0) {
        return redirect()->back()->with('error', 'Gagal hapus! Satuan ini masih digunakan oleh produk.');
    }

    $unit->delete();
    return redirect()->back()->with('success', 'Satuan berhasil dihapus.');
}
public function show($id) {
    return redirect()->route('units.index');
}
}
