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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->string('codigoCompra')->unique();
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade');
            $table->foreignId('comprobante_id')->nullable()->constrained('comprobantes')->onDelete('cascade');
            $table->foreignId('estado_transaccion_id')->constrained('estado_transacciones')->onDelete('cascade');
            $table->date('fecha_envio')->nullable();
            $table->date('fecha_cotizacion')->nullable();
            $table->date('fecha_aprobacion')->nullable();
            $table->date('fecha_entrega_estimada')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('igv', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notas_proveedor')->nullable();
            $table->string('condiciones_pago')->nullable();
            $table->integer('dias_credito')->nullable();
            $table->string('pdf_cotizacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
