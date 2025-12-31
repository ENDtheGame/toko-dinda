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
        Schema::table('brands', function (Blueprint $table) {
            // Hapus kolom lama jika masih ada
            $table->dropColumn('main_category');

            // Tambah kolom category_id yang terhubung ke tabel categories
            $table->foreignId('category_id')
                ->nullable()
                ->after('sales_phone')
                ->constrained('categories') // Asumsi nama tabel kategori kamu adalah 'categories'
                ->onDelete('set null'); // Jika kategori dihapus, brand tidak ikut terhapus tapi jadi null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_id_on_brands', function (Blueprint $table) {
            //
        });
    }
};
