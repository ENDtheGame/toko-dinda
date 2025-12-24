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
    $categories = Category::whereNull('parent_id')->with('children')->get();
    // Ambil produk, jika ada filter kategori maka filter datanya
    $products = Product::when($request->category_id, function($query) use ($request) {
        return $query->where('category_id', $request->category_id);
    })->get();

    return view('kasir.index', compact('categories', 'products'));
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
        return response()->json(['success' => true, 'message' => 'Transaksi Berhasil!']);
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}
}
