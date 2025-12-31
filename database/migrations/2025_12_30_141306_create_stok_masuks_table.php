<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_entries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained('products'); // Relasi ke 'products'
    $table->integer('quantity_added'); // Jumlah yang baru masuk
    $table->bigInteger('actual_purchase_price'); // Harga beli saat barang datang
    $table->string('supplier')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_masuks');
    }
};
