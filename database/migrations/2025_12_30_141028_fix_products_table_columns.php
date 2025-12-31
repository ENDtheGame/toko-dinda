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
    Schema::table('products', function (Blueprint $table) {
        // 1. Jika ada kolom 'unit' yang lama, kita hapus/ganti
        if (Schema::hasColumn('products', 'unit')) {
            $table->dropColumn('unit');
        }

        // 2. Tambahkan kolom unit_id jika belum ada
        if (!Schema::hasColumn('products', 'unit_id')) {
            $table->foreignId('unit_id')
      ->nullable() // Izinkan kosong
      ->constrained('units')
      ->onDelete('set null'); // Jika unit dihapus, unit_id di produk jadi NULL
        }

        // 3. Tambahkan kolom grosir jika belum ada
        if (!Schema::hasColumn('products', 'wholesale_min')) {
            $table->integer('wholesale_min')->nullable()->after('stock');
        }
        if (!Schema::hasColumn('products', 'wholesale_price')) {
            $table->decimal('wholesale_price', 15, 2)->nullable()->after('wholesale_min');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
