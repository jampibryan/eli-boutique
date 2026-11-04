<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('tipo_documento', 3)->default('DNI');
            $table->string('direccionCliente')->nullable();
        });
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('direccion_factura')->nullable();
        });
        // Preserve the existing non-null gender FK without assigning a gender to a company.
        if (!DB::table('tipo_generos')->where('descripcionTG', 'No aplica')->exists()) {
            DB::table('tipo_generos')->insert(['descripcionTG'=>'No aplica', 'created_at'=>now(), 'updated_at'=>now()]);
        }
    }

    public function down(): void
    {
        Schema::table('ventas', fn (Blueprint $table) => $table->dropColumn('direccion_factura'));
        Schema::table('clientes', fn (Blueprint $table) => $table->dropColumn(['tipo_documento','direccionCliente']));
        // The catalog value can still be referenced by clients; keep it.
    }
};
