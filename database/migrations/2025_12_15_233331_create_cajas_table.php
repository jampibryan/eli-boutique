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
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->string('codigoCaja', 6)->unique(); // Código de caja único
            $table->date('fecha')->unique(); // Fecha de la caja (única por día)
            $table->time('hora_cierre')->nullable(); // Hora de cierre de la caja
            $table->integer('clientesHoy')->default(0); // Clientes del día
            $table->integer('productosVendidos')->default(0); // Productos vendidos hoy
            $table->decimal('ingresoDiario', 10, 2)->default(0.00); // Ingresos del día
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cajas');
    }
};
