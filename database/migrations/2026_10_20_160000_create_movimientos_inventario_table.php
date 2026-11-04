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
        if (!Schema::hasTable('movimientos_inventario')) {
            Schema::create('movimientos_inventario', function (Blueprint $table) {
                $table->id();
                $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
                $table->foreignId('producto_talla_id')->constrained('producto_tallas')->onDelete('cascade');
                $table->string('tipo_movimiento', 50); // INGRESO_COMPRA, SALIDA_VENTA, REVERSION_VENTA, AJUSTE_INVENTARIO, INVENTARIO_INICIAL
                $table->integer('cantidad'); // Positivo (ingreso) o Negativo (salida)
                $table->decimal('costo_unitario', 10, 2)->default(0.00);
                $table->decimal('precio_unitario', 10, 2)->nullable();
                $table->integer('stock_anterior');
                $table->integer('stock_nuevo');
                $table->string('origen_tipo')->nullable(); // Polimórfico (App\Models\Venta, App\Models\Compra, etc.)
                $table->unsignedBigInteger('origen_id')->nullable();
                $table->string('referencia_documento', 100)->nullable(); // Ej. 'Venta 0000123', 'Compra 0000045'
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->text('motivo')->nullable();
                $table->timestamps();

                // Índices de alta velocidad para trazabilidad y auditoría
                $table->index(['producto_id', 'producto_talla_id'], 'idx_mov_prod_talla');
                $table->index('tipo_movimiento', 'idx_mov_tipo');
                $table->index(['origen_tipo', 'origen_id'], 'idx_mov_origen');
                $table->index('created_at', 'idx_mov_created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
