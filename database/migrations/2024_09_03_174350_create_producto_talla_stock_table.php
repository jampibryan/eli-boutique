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
        Schema::create('producto_talla_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('producto_talla_id')->constrained('producto_tallas')->onDelete('cascade');
            $table->integer('stock')->default(0);
            $table->timestamps();
            
            // Evitar duplicados: un producto no puede tener la misma talla dos veces
            $table->unique(['producto_id', 'producto_talla_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_talla_stock');
    }
};
