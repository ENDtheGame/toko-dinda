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
        // Cek dulu apakah kolom 'category' masih ada sebelum dihapus
        if (Schema::hasColumn('products', 'category')) {
            $table->dropColumn('category');
        }

        // Pastikan kolom category_id ditambahkan jika belum ada
        if (!Schema::hasColumn('products', 'category_id')) {
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
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
