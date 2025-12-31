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
        // Tambahkan kolom grosir setelah selling_price
        $table->integer('wholesale_min')->nullable()->after('selling_price');
        $table->decimal('wholesale_price', 15, 2)->nullable()->after('wholesale_min');

        // Pastikan kolom unit_id juga ada (karena di form kamu pakai unit_id)
        if (!Schema::hasColumn('products', 'unit_id')) {
            $table->foreignId('unit_id')->nullable()->constrained('units')->after('category_id');
        }
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            //
        });
    }
};
