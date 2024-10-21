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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigoP');
            $table->foreignId('categoria_producto_id')->constrained()->onDelete('cascade');
            $table->string('imagenP')->nullable();
            $table->text('descripcionP')->nullable();
            $table->decimal('precioP', 8, 2);
            $table->integer('stockP');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
