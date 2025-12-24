<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale; // Menggunakan Model Sale hasil migrasi baru
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    /**
     * Export data penjualan ke CSV dengan filter tanggal.
     */
    public function exportCSV(Request $request)
    {
        $filename = "laporan_penjualan_" . date('Y-m-d') . ".csv";

        // Filter data berdasarkan range tanggal (jika ada input dari user)
        $query = Sale::with('user'); // Eager loading untuk menghindari N+1 query pada data kasir

        if ($request->has(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $data = $query->latest()->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Header kolom CSV
            fputcsv($file, ['ID Transaksi', 'Tanggal', 'Kasir', 'Total Item', 'Total Bayar']);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->invoice_number,
                    $row->created_at->format('d-m-Y H:i'),
                    $row->user->name ?? '—',
                    $row->saleDetails->sum('quantity'), // Menghitung total barang terjual
                    $row->total_price
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
