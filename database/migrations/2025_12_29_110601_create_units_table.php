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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Dus, Karung, Pack, Pcs, Kg
            // Relasi ke dirinya sendiri (Induk Satuan)
            $table->foreignId('parent_id')->nullable()->constrained('units')->onDelete('cascade');
            // Nilai konversi (Misal: 1 Dus isi 40, maka isinya 40.00)
            $table->decimal('base_quantity', 10, 2)->default(1);
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
