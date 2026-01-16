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
        Schema::create('venta_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained()->onDelete('cascade');
            $table->foreignId('producto_id')->constrained()->onDelete('cascade');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2); // Precio final con IGV incluido
            $table->decimal('base_imponible', 10, 2); // Precio sin IGV (precio_unitario / 1.18)
            $table->decimal('igv', 10, 2); // Monto del IGV (precio_unitario - base_imponible)
            $table->decimal('subtotal', 10, 2); // Total de la línea (cantidad * precio_unitario)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_detalles');
    }
};
