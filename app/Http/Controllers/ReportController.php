<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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
    $month = $request->get('month', date('m'));
    $year = $request->get('year', date('Y'));

    // Pastikan variabel $sales selalu terisi (minimal array kosong, bukan null)
    $sales = Sale::whereMonth('created_at', $month)
                 ->whereYear('created_at', $year)
                 ->get();

    return view('reports.monthly', compact('sales', 'month', 'year'));
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

        return $pdf->download("laporan-harian-{$date}.pdf");
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
        return $pdf->download('laporan-stok-seluruh-produk.pdf');
    }
}
