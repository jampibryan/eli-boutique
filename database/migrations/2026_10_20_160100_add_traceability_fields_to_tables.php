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
        // 1. Agregar costo de adquisición al catálogo de productos
        if (Schema::hasTable('productos')) {
            Schema::table('productos', function (Blueprint $table) {
                if (!Schema::hasColumn('productos', 'costo_compra')) {
                    $table->decimal('costo_compra', 10, 2)->default(0.00)->after('precioP');
                }
            });
        }

        // 2. Congelar costo de compra al momento de la venta para calcular margen
        if (Schema::hasTable('venta_detalles')) {
            Schema::table('venta_detalles', function (Blueprint $table) {
                if (!Schema::hasColumn('venta_detalles', 'costo_unitario')) {
                    $table->decimal('costo_unitario', 10, 2)->default(0.00)->after('precio_unitario');
                }
            });
        }

        // 3. Soporte para medios de pago en caja (Efectivo, Yape, Plin, Tarjeta, etc.)
        if (Schema::hasTable('pagos')) {
            Schema::table('pagos', function (Blueprint $table) {
                if (!Schema::hasColumn('pagos', 'metodo_pago')) {
                    $table->string('metodo_pago', 50)->default('efectivo')->after('comprobante_id');
                }
                if (!Schema::hasColumn('pagos', 'referencia_pago')) {
                    $table->string('referencia_pago', 100)->nullable()->after('metodo_pago');
                }
            });
        }

        // 4. Soporte para comprobante emitido por el proveedor
        if (Schema::hasTable('compras')) {
            Schema::table('compras', function (Blueprint $table) {
                if (!Schema::hasColumn('compras', 'numero_documento_proveedor')) {
                    $table->string('numero_documento_proveedor', 50)->nullable()->after('codigoCompra');
                }
                if (!Schema::hasColumn('compras', 'tipo_documento_proveedor')) {
                    $table->string('tipo_documento_proveedor', 50)->nullable()->after('numero_documento_proveedor');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('productos') && Schema::hasColumn('productos', 'costo_compra')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->dropColumn('costo_compra');
            });
        }

        if (Schema::hasTable('venta_detalles') && Schema::hasColumn('venta_detalles', 'costo_unitario')) {
            Schema::table('venta_detalles', function (Blueprint $table) {
                $table->dropColumn('costo_unitario');
            });
        }

        if (Schema::hasTable('pagos')) {
            Schema::table('pagos', function (Blueprint $table) {
                if (Schema::hasColumn('pagos', 'metodo_pago')) {
                    $table->dropColumn('metodo_pago');
                }
                if (Schema::hasColumn('pagos', 'referencia_pago')) {
                    $table->dropColumn('referencia_pago');
                }
            });
        }

        if (Schema::hasTable('compras')) {
            Schema::table('compras', function (Blueprint $table) {
                if (Schema::hasColumn('compras', 'numero_documento_proveedor')) {
                    $table->dropColumn('numero_documento_proveedor');
                }
                if (Schema::hasColumn('compras', 'tipo_documento_proveedor')) {
                    $table->dropColumn('tipo_documento_proveedor');
                }
            });
        }
    }
};
