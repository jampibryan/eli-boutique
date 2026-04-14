<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            if (!Schema::hasColumn('venta_detalles', 'producto_talla_id')) {
                $table->foreignId('producto_talla_id')
                    ->nullable()
                    ->after('producto_id')
                    ->constrained('producto_tallas')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            if (Schema::hasColumn('venta_detalles', 'producto_talla_id')) {
                $table->dropConstrainedForeignId('producto_talla_id');
            }
        });
    }
};
