<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockEntry; // Pastikan kamu punya model ini untuk riwayat
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockEntryController extends Controller
{

    public function index()
    {
        // Pastikan nama variabel di sini...
        $histories = \App\Models\StockEntry::with('product')
            ->latest()
            ->paginate(15);

        // ...Sama dengan nama di dalam compact ini
        return view('stock_histories.index', compact('histories'));
    }

    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity_added' => 'required|numeric|min:1',
            'actual_purchase_price' => 'nullable|numeric',
            'supplier' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // 2. Ambil data produk
            $product = Product::findOrFail($request->product_id);

            // 3. Update stok di tabel products (Tambah stok lama dengan stok baru)
            $product->increment('stock', $request->quantity_added);

            // 4. (Opsional) Update harga beli jika admin mengisi harga baru
            if ($request->filled('actual_purchase_price')) {
                // Jika harga yang diinput adalah harga total (misal 1 karung),
                // bagi dulu dengan konversi satuan agar jadi harga per pcs/kg
                $konversi = $product->unit->base_quantity ?? 1;
                $product->purchase_price = $request->actual_purchase_price / $konversi;
                $product->save();
            }

            // 5. Simpan riwayat ke tabel stock_entries (Jika ada tabelnya)
            // Jika belum ada tabel riwayat, bagian ini bisa dihapus dulu
            /*
            StockEntry::create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity_added,
                'supplier' => $request->supplier,
            ]);
            */

            DB::commit();

            return redirect()->back()->with('success', "Stok {$product->name} berhasil ditambah sebanyak {$request->quantity_added} unit dasar!");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
