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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id(); // Crea una columna id auto-incremental como clave primaria
            $table->string('nombreCliente');
            $table->string('apellidoCliente');
            $table->string('dniCliente')->unique();
            $table->string('correoCliente')->unique();
            $table->string('telefonoCliente')->unique();
            $table->foreignId('tipo_genero_id')->constrained()->onDelete('cascade');
            // Columna para la clave foránea que referencia a la tabla tipo_generos
            // `constrained()` automáticamente establece la relación con la columna `id` en la tabla `tipo_generos`
            // `onDelete('cascade')` asegura que si un `TipoGenero` se elimina, todos los `Clientes` asociados también se eliminarán
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
