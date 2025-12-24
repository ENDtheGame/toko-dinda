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
        Schema::create('stock_mutations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained();
    $table->enum('type', ['in', 'out']); // Masuk atau Keluar
    $table->integer('amount');
    $table->string('note')->nullable(); // Contoh: "Kulakan dari Supplier A"
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_mutations');
    }
};
