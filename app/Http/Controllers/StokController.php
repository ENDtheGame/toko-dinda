<?php

namespace App\Http\Controllers;

use App\Models\Product; // Menggunakan model Product
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function exportCSV()
    {
        $filename = "laporan_stok_grosir_" . date('Y-m-d') . ".csv";

        // Mengambil semua produk, diurutkan dari stok terkecil agar pemilik tahu apa yang harus dibeli lagi
        $data = Product::orderBy('stock', 'asc')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Header kolom sesuai kolom di migration Product baru
            fputcsv($file, ['Kode Barang', 'Nama Barang', 'Stok', 'Satuan', 'Harga Modal', 'Harga Jual']);

            // Isi data
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->code,           // Kolom code dari migration baru
                    $row->name,           // Kolom name dari migration baru
                    $row->stock,          // Kolom stock
                    $row->unit,           // Satuan (Dus/Pcs)
                    $row->purchase_price, // Harga Modal
                    $row->selling_price   // Harga Jual
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
