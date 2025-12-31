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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // Harga jual per satuan tersebut
            $table->decimal('price', 15, 2);
            // Stok minimal untuk peringatan
            $table->integer('min_stock')->default(0);
            $table->integer('stock')->default(0);
            $table->decimal('selling_price', 15, 2);
            $table->engine = 'InnoDB';
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
