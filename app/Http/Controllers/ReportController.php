<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dashboard()
    {
        // 1. Ringkasan Statistik
        $totalProducts = Product::count();
        // Menggunakan created_at sesuai standar Laravel migration
        $dailySales = Sale::whereDate('created_at', Carbon::today())->sum('total_price');
        $monthlySales = Sale::whereMonth('created_at', Carbon::now()->month)
                            ->whereYear('created_at', Carbon::now()->year)
                            ->sum('total_price');

        // 2. Alert Stok Rendah (Sangat penting untuk Grosir)
        $lowStockProducts = Product::where('stock', '<', 10)->get();
        $lowStockCount = $lowStockProducts->count();

        // 3. Data Grafik (7 Hari Terakhir)
        $salesData = Sale::selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dates = $salesData->pluck('date');
        $totals = $salesData->pluck('total');

        return view('reports.dashboard', compact(
            'totalProducts', 'dailySales', 'monthlySales',
            'lowStockProducts', 'lowStockCount', 'dates', 'totals'
        ));
        $topProducts = \App\Models\SaleDetail::select('product_id', \DB::raw('SUM(quantity) as total_qty'))
        ->with('product')
        ->groupBy('product_id')
        ->orderBy('total_qty', 'desc')
        ->take(5)
        ->get();

    // Pastikan semua variabel dikirim ke view dashboard
    return view('dashboard', [
        'totalProducts' => $totalProducts ?? 0,
        'dailySales' => $dailySales ?? 0,
        'monthlySales' => $monthlySales ?? 0,
        'lowStockCount' => $lowStockCount ?? 0,
        'lowStockProducts' => $lowStockProducts ?? collect(),
        'dates' => $dates ?? [],
        'totals' => $totals ?? [],
        'topProducts' => $topProducts // <--- Tambahkan ini
    ]);
    }

    public function daily(Request $request)
    {
        // Fitur tambahan: Bisa pilih tanggal, jika tidak default hari ini
        $date = $request->get('date', Carbon::today()->toDateString());
        $sales = Sale::with('user')->whereDate('created_at', $date)->get();
        $totalSales = $sales->sum('total_price');

        return view('reports.daily', compact('sales', 'totalSales', 'date'));
    }
    public function monthly(Request $request)
    {
        // Mengambil input bulan dan tahun, default ke bulan/tahun saat ini
        $month = $request->input('month', (int)date('m'));
        $year = $request->input('year', (int)date('Y'));

        // Gunakan model Sales
        $sales = Sale::whereMonth('created_at', $month)
                      ->whereYear('created_at', $year)
                      ->orderBy('created_at', 'desc')
                      ->get();

        // Hitung total pendapatan dari kolom total_price
        $totalSales = $sales->sum('total_price');

        // Kirim ke view
        return view('reports.monthly', compact('sales', 'totalSales', 'month', 'year'));
    }

    public function dailyPdf(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        $sales = Sale::whereDate('created_at', $date)->get();
        $totalSales = $sales->sum('total_price');

        $pdf = Pdf::loadView('reports.daily_pdf', [
            'sales' => $sales,
            'totalSales' => $totalSales,
            'date' => $date
        ]);

        return $pdf->stream("laporan-harian-{$date}.pdf");
    }

public function exportPdf($month, $year)
{
   // Di dalam function laporan bulanan kamu
$sales = Sale::with(['details.product']) // Load relasi detail dan produknya
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

    $totalSales = $sales->sum('total_price');

    // Pastikan kamu sudah buat file: resources/views/reports/monthly_pdf.blade.php
    $pdf = Pdf::loadView('reports.monthly_pdf', compact('sales', 'totalSales', 'month', 'year'));

    return $pdf->stream("laporan-penjualan-{$month}-{$year}.pdf");
}

    public function stock()
    {
        // Menampilkan semua stok tapi highlight yang rendah
        $products = Product::orderBy('stock', 'asc')->get();
        return view('reports.stock', compact('products'));
    }

    public function stockPdf()
    {
        $products = Product::all();
        $pdf = Pdf::loadView('reports.stock_pdf', compact('products'));
        return $pdf->stream('laporan-stok-seluruh-produk.pdf');
    }

    public function destroySale($id)
{
    DB::beginTransaction();

    try {
        // Load sale beserta detailnya
        $sale = Sale::with('details')->findOrFail($id);

        foreach ($sale->details as $detail) {
            // Ambil produk berdasarkan product_id di tabel sale_details
            $product = Product::find($detail->product_id);

            if ($product) {
                // Pastikan nama kolom di tabel products kamu adalah 'stock'
                // Jika nama kolomnya 'stok' (pakai 'k'), ganti di bawah ini
                $product->increment('stock', $detail->quantity);
            }
        }

        // Hapus sale (pastikan di database/migration sudah ada onDelete('cascade'))
        $sale->delete();

        DB::commit();
        return redirect()->back()->with('success', 'Transaksi dihapus & stok kembali!');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
    }
}
}
