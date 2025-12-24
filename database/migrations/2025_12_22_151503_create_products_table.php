<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
   Schema::table('products', function (Blueprint $table) {
    // Satuan utama (misal: Pcs)
    $table->foreignId('unit_id')->constrained('units');
    // Harga jual per satuan tersebut
    $table->decimal('price', 15, 2);
    // Stok minimal untuk peringatan
    $table->integer('min_stock')->default(0);
});
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
