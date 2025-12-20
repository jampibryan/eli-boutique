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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->nullable()->constrained('cajas');
            $table->string('codigoVenta', 7)->unique();  // Tamaño ajustado a 7 caracteres
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');  // Llave foránea a la tabla clientes
            $table->foreignId('estado_transaccion_id')->constrained('estado_transacciones')->onDelete('cascade');  // Llave foránea a la tabla estados de transacción
            $table->decimal('subTotal', 10, 2);
            $table->decimal('IGV', 10, 2);
            $table->decimal('montoTotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
