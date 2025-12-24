<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;
// 1. Grouping Admin
// 1. Fitur KHUSUS Admin (Kasir tidak bisa buka ini)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('dashboard');

    // Grouping Report
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/daily', [ReportController::class, 'daily'])->name('daily');
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
        Route::get('/stock', [ReportController::class, 'stock'])->name('stock');
        Route::get('/daily/pdf', [ReportController::class, 'dailyPdf'])->name('daily.pdf');
        Route::get('/monthly/pdf', [ReportController::class, 'monthlyPdf'])->name('monthly.pdf');
        Route::get('/stock/pdf', [ReportController::class, 'stockPdf'])->name('stock.pdf');
    });

    Route::get('/laporan/export-csv', [LaporanController::class, 'exportCSV'])->name('laporan.export.csv');
    Route::get('/stok/export-csv', [StokController::class, 'exportCSV'])->name('stok.export.csv');
});

// 2. Fitur BERSAMA (Bisa dibuka Admin DAN Kasir)
Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('transaction', TransactionController::class);
    Route::get('/kasir', [TransactionController::class, 'index'])->name('kasir.index');
    Route::post('/kasir/transaksi', [TransactionController::class, 'store'])->name('kasir.store');
    Route::resource('sales', SalesController::class)->only(['index', 'create', 'store']);
    // Hanya mengizinkan method yang benar-benar ada di Controller
    Route::resource('units', UnitController::class);
    Route::delete('/units/{unit}', [UnitController::class, 'destroy'])->name('units.destroy');

});
// Profile Routes (accessible by all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// Public Route
Route::get('/', function () {
    return view('welcome');
});
require __DIR__.'/auth.php';

