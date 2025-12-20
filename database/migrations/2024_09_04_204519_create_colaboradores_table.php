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
        Schema::create('colaboradores', function (Blueprint $table) {
            $table->id();
            $table->string('nombreColab');
            $table->string('apellidosColab');
            $table->string('dniColab')->unique();
            $table->integer('edadColab');
            $table->string('correoColab')->unique();
            $table->string('telefonoColab')->unique();

            // Llaves foráneas
            $table->foreignId('cargo_id')->constrained()->onDelete('cascade');
            $table->foreignId('tipo_genero_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colaboradores');
    }
};