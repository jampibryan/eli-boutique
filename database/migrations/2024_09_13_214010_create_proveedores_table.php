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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombreEmpresa');
            $table->string('nombreProveedor');
            $table->string('apellidoProveedor');
            $table->string('RUC')->unique();
            $table->string('direccionProveedor');
            $table->string('correoProveedor')->unique();
            $table->string('telefonoProveedor')->unique();

            // Llaves foráneas
            // $table->foreignId('tipo_proveedor_id')->constrained('tipo_proveedores')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
