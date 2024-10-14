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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('compra_id')->nullable()->constrained()->onDelete('cascade');

            $table->foreignId('comprobante_id')->constrained()->onDelete('cascade'); // Llave foránea a la tabla comprobantes
            $table->decimal('importe', 10, 2);
            $table->decimal('vuelto', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
