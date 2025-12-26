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
        'base_quantity' => 'nullable|numeric|min:1', // Ubah jadi nullable agar tidak error di validasi
    ]);

    // LOGIKA PERBAIKAN:
    // Jika tidak ada parent_id, maka base_quantity WAJIB 1 (satuan dasar)
    // Jika ada parent_id tapi base_quantity kosong, beri default 1
    $data = $request->all();
    if (empty($request->parent_id) || empty($request->base_quantity)) {
        $data['base_quantity'] = 1;
    }

    Unit::create($data);

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
