<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail; // Pastikan model ini sudah dibuat
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function index()
    {
        // Menampilkan riwayat transaksi terbaru
        $sales = Sale::with('user')->latest()->paginate(10);
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        // Validasi input array (karena grosir beli banyak item)
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'pay_amount' => 'required|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $totalPrice = 0;
                $invoiceNumber = 'INV-' . now()->format('YmdHis');

                // 1. Buat Header Penjualan
                $sale = Sale::create([
                    'invoice_number' => $invoiceNumber,
                    'user_id' => Auth::id(), // Mengambil ID kasir yang login
                    'total_price' => 0, // Akan diupdate setelah hitung detail
                    'pay_amount' => $request->pay_amount,
                    'change_amount' => 0,
                ]);

                foreach ($request->items as $item) {
                    $product = Product::lockForUpdate()->find($item['product_id']);

                    // Cek Stok
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stok barang {$product->name} tidak mencukupi!");
                    }

                    $subtotal = $product->selling_price * $item['quantity'];
                    $totalPrice += $subtotal;

                    // 2. Simpan Detail Penjualan
                    $sale->saleDetails()->create([
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price_at_sale' => $product->selling_price,
                        'subtotal' => $subtotal,
                    ]);

                    // 3. Kurangi Stok Produk
                    $product->decrement('stock', $item['quantity']);
                }

                // 4. Update Total & Kembalian di Header
                $sale->update([
                    'total_price' => $totalPrice,
                    'change_amount' => $request->pay_amount - $totalPrice,
                ]);

                return redirect()->route('sales.index')->with('success', 'Transaksi ' . $invoiceNumber . ' Berhasil!');
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
