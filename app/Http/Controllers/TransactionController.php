<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
{
    // 1. Ambil semua kategori untuk tombol filter
    // Ambil yang parent_id null saja jika ingin sistem dropdown induk-anak
    $categories = Category::whereNull('parent_id')->with('children')->get();

    // 2. Mulai query Produk
    $query = Product::with(['category', 'unit']);

    // 3. LOGIKA FILTER: Jika ada category_id di URL
    if ($request->has('category_id') && $request->category_id != '') {
        $categoryId = $request->category_id;

        $query->where(function($q) use ($categoryId) {
            // Cek apakah produk ada di kategori tersebut
            $q->where('category_id', $categoryId)
              // ATAU cek apakah produk ada di anak-anak kategori tersebut (sub-kategori)
              ->orWhereHas('category', function($subQ) use ($categoryId) {
                  $subQ->where('parent_id', $categoryId);
              });
        });
    }

    // 4. Eksekusi query
    $products = $query->where('stock', '>', 0)->get();

    return view('kasir.index', compact('products', 'categories'));
}

    public function store(Request $request)
{
    DB::beginTransaction();
    try {
        $sale = Sale::create([
            'invoice_number' => 'INV-' . time(),
            'total_price' => $request->total_price,
            'pay_amount' => $request->pay_amount,
            'change_amount' => $request->pay_amount - $request->total_price,
            'user_id' => Auth::id(),
        ]);

        foreach ($request->items as $item) {
            SaleDetail::create([
                'sale_id' => $sale->id,
                'product_id' => $item['id'],
                'quantity' => $item['qty'],
                'price' => $item['selling_price'],
                'subtotal' => $item['selling_price'] * $item['qty'],
            ]);

            // PENTING: Kurangi stok barang
            $product = Product::find($item['id']);
            $product->decrement('stock', $item['qty']);
        }

       DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Transaksi Berhasil!',
            'invoice_number' => $sale->invoice_number // Tambahkan ini
        ]);
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}
}
