<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Menggunakan Type Hinting untuk keamanan
    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit', 'brand']);

        // Logika Pencarian
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhereHas('category', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
        }

        // Mengambil data dengan pagination (misal 10 data per halaman)
        // appends(request()->all()) penting agar saat pindah halaman search tidak hilang
        $products = $query->latest()->paginate(10)->withQueryString();

        $categories = Category::all();
        $units = Unit::all();
        $brands = Brand::all();

        return view('products.index', compact('products', 'categories', 'units', 'brands'));
    }

    public function create()
    {
        $categories = Category::all();
        $units = Unit::all(); // Ambil semua satuan untuk dipilih
        $brands = Brand::all();
        return view('products.create', compact('categories', 'units', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'brand_id' => 'required|exists:brands,id',
            'purchase_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'stock' => 'required|numeric', // Ini sudah total pcs dari javascript modal
            'wholesale_min' => 'nullable|numeric',
            'wholesale_price' => 'nullable|numeric',
        ]);

        // 1. Ambil data satuan untuk tahu nilai konversinya
        $unit = Unit::find($request->unit_id);
        $konversi = $unit->base_quantity ?? 1;

        // 2. Siapkan data yang akan disimpan
        $data = $request->all();

        // 3. LOGIKA ANALOGI: Bagi harga input dengan jumlah isi barang
        // Misal: Harga Dus 140.000 / 40 pcs = 3.500 (yang disimpan ke DB)
        $data['purchase_price'] = $request->purchase_price / $konversi;
        $data['selling_price'] = $request->selling_price / $konversi;

        // 4. Simpan ke database
        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil disimpan dengan harga satuan terkecil!');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'purchase_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        // Karena di modal edit kita biasanya langsung edit harga satuan (pcs),
        // maka kita bisa langsung update tanpa membagi lagi,
        // KECUALI jika kamu menambah pilihan satuan di modal edit nantinya.
        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    // Menggunakan Route Model Binding (langsung panggil Product $product)
    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::all(); // Ambil semua data brand
        return view('products.edit', compact('product', 'categories', 'brands'));
    }


    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || count($ids) == 0) {
            return back()->with('error', 'Pilih produk yang ingin dihapus terlebih dahulu.');
        }

        // Menghapus semua produk yang ID-nya ada dalam array $ids
        Product::whereIn('id', $ids)->delete();

        return back()->with('success', count($ids) . ' produk berhasil dihapus secara massal.');
    }

    public function restok(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'product_id'     => 'required|exists:products,id',
            'quantity_added' => 'required|numeric|min:0.1',
            'actual_purchase_price' => 'nullable|numeric',
            'supplier'       => 'nullable|string',
        ]);

        // 2. Cari Produk
        $product = Product::findOrFail($request->product_id);

        // 3. Tambahkan Stok (quantity_added sudah dalam satuan dasar/pcs dari JS)
        $product->increment('stock', $request->quantity_added);

        // 4. Update Harga Beli jika ada input harga total baru
        if ($request->filled('actual_purchase_price')) {
            $konversi = $product->unit->base_quantity ?? 1;
            // Kita bagi harga total nota dengan konversi agar jadi harga per satuan dasar
            $product->purchase_price = $request->actual_purchase_price / $konversi;
            $product->save();
        }

        // 5. Kembalikan dengan pesan sukses
        return redirect()->back()->with('success', "Stok {$product->name} berhasil ditambah sebanyak {$request->quantity_added}!");
    }
}
