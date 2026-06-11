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
        Schema::create('venta_documentos_sunat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->string('uuid')->unique();
            $table->string('estado_envio')->default('pendiente'); // 'pendiente', 'enviando', 'aceptado', 'rechazado', 'error'
            $table->string('xml_path')->nullable();
            $table->string('cdr_path')->nullable();
            $table->string('signature_hash')->nullable();
            $table->string('codigo_respuesta_sunat')->nullable();
            $table->text('descripcion_respuesta_sunat')->nullable();
            $table->unsignedInteger('intentos_envio')->default(0);
            $table->timestamp('fecha_envio')->nullable();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_documentos_sunat');
    }
};
