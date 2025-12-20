<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\EstadoTransaccion;
use App\Models\Comprobante;
use App\Models\Pago;
use Carbon\Carbon;

class CompraSeeder extends Seeder
{
    // COMPRAS REALISTAS - 4 SÁBADOS DE OCTUBRE
    private $comprasSincronizadas = [
        // ============ SÁBADO 4 OCT ============
        // Semana 1: Compra productos más vendidos y con stock bajo
        [
            'fecha' => '2025-10-04 16:30:00',
            'comprobante' => 'Factura',
            'productos' => [
                // PRODUCTOS MÁS VENDIDOS semana 1 + stock seguridad
                ['id' => 1, 'comprar' => 20],   // Polo clásico - Vendió ~9, compra 20
                ['id' => 2, 'comprar' => 15],   // Camiseta básica - Vendió ~9, compra 15
                ['id' => 6, 'comprar' => 12],   // Jeans skinny - Vendió ~6, compra 12
                
                // PRODUCTOS que bajaron a stock crítico (≤10)
                ['id' => 23, 'comprar' => 10],  // Sudadera capucha - Stock bajo
                ['id' => 19, 'comprar' => 8],   // Chaqueta bomber - Tendencia
                ['id' => 22, 'comprar' => 6],   // Leggings yoga - Nueva demanda
                
                // Total: 6 productos (no 25)
            ]
        ],
        
        // ============ SÁBADO 11 OCT ============
        // Semana 2: Reposición estratégica
        [
            'fecha' => '2025-10-11 16:45:00',
            'comprobante' => 'Factura',
            'productos' => [
                // Productos que SIGUEN vendiéndose
                ['id' => 1, 'comprar' => 12],   // Polo clásico - Demanda constante
                ['id' => 23, 'comprar' => 8],   // Sudadera - Más frío
                
                // NUEVOS productos que bajaron de stock
                ['id' => 3, 'comprar' => 7],    // Polo manga larga - Rotación media
                ['id' => 10, 'comprar' => 5],   // Pantalón cargo
                ['id' => 16, 'comprar' => 5],   // Chaqueta denim
                
                // Producto en TENDENCIA (más ventas)
                ['id' => 19, 'comprar' => 10],  // Chaqueta bomber - Más popular
                
                // Total: 6 productos
            ]
        ],
        
        // ============ SÁBADO 18 OCT ============
        // Semana 3: Ajustes antes de fin de mes
        [
            'fecha' => '2025-10-18 17:00:00',
            'comprobante' => 'Factura',
            'productos' => [
                // PRODUCTOS CRÍTICOS (stock ≤8)
                ['id' => 2, 'comprar' => 15],   // Camiseta básica - Stock muy bajo
                ['id' => 19, 'comprar' => 12],  // Chaqueta bomber - Éxito total
                
                // Productos POPULARES que se vacían
                ['id' => 1, 'comprar' => 10],   // Polo clásico
                ['id' => 6, 'comprar' => 8],    // Jeans skinny
                ['id' => 23, 'comprar' => 10],  // Sudadera
                
                // Productos con stock INSUFICIENTE
                ['id' => 22, 'comprar' => 8],   // Leggings yoga
                ['id' => 25, 'comprar' => 6],   // Top deportivo
                
                // Total: 7 productos
            ]
        ],
        
        // ============ SÁBADO 25 OCT ============
        // Semana 4: Última compra del mes - Enfoque noviembre
        [
            'fecha' => '2025-10-25 17:15:00',
            'comprobante' => 'Factura',
            'productos' => [
                // PARA TEMPORADA DE FRÍO (noviembre)
                ['id' => 23, 'comprar' => 15],  // Sudadera - Previsión alta demanda
                ['id' => 19, 'comprar' => 12],  // Chaqueta bomber
                ['id' => 17, 'comprar' => 8],   // Abrigo trench
                
                // PRODUCTOS BÁSICOS (siempre necesarios)
                ['id' => 1, 'comprar' => 18],   // Polo clásico
                ['id' => 2, 'comprar' => 15],   // Camiseta básica
                ['id' => 6, 'comprar' => 10],   // Jeans skinny
                
                // Productos que QUEDARON BAJOS
                ['id' => 4, 'comprar' => 7],    // Camiseta estampada
                ['id' => 8, 'comprar' => 5],    // Pantalón vestir (poca rotación, poco)
                
                // Total: 8 productos
                // NOTA: NO se compran #11, #12, #15, #20, #24 (baja rotación)
            ]
        ],
    ];

    public function run()
    {
        echo "🛒 COMPRASEEDER REALISTA - Compras selectivas (solo productos necesarios)\n";
        echo str_repeat("=", 60) . "\n\n";
        
        // Obtener datos básicos
        $proveedor = Proveedor::first();
        $estadoRecibido = EstadoTransaccion::where('descripcionET', 'Recibido')->first();
        
        if (!$proveedor || !$estadoRecibido) {
            echo "❌ Faltan datos básicos\n";
            return;
        }
        
        $totalCompras = 0;
        $totalProductosComprados = 0;
        
        foreach ($this->comprasSincronizadas as $semana => $compraData) {
            $semanaNum = $semana + 1;
            $fecha = $compraData['fecha'];
            $carbonFecha = Carbon::parse($fecha);
            $tipoComprobante = $compraData['comprobante'];
            
            // Obtener comprobante Factura
            $comprobante = Comprobante::where('descripcionCOM', $tipoComprobante)->first();
            
            echo "📦 SÁBADO {$semanaNum} - {$carbonFecha->format('d/m/Y H:i')}\n";
            echo "   Estrategia: " . count($compraData['productos']) . " productos (no todos)\n";
            
            // Crear compra
            $compra = Compra::create([
                'proveedor_id' => $proveedor->id,
                'estado_transaccion_id' => $estadoRecibido->id,
                'codigoCompra' => 'COMP-' . str_pad($semanaNum, 3, '0', STR_PAD_LEFT),
                'created_at' => $carbonFecha,
                'updated_at' => $carbonFecha,
            ]);

            $totalCompra = 0;
            $productosEnEstaCompra = 0;
            
            foreach ($compraData['productos'] as $item) {
                // Validar que el ID esté entre 1-25
                if ($item['id'] < 1 || $item['id'] > 25) {
                    echo "   ❌ Producto #{$item['id']} no existe (IDs válidos: 1-25)\n";
                    continue;
                }
                
                $producto = Producto::find($item['id']);
                
                if (!$producto) {
                    echo "   ❌ Producto #{$item['id']} no encontrado en la BD\n";
                    continue;
                }
                
                $stockAnterior = $producto->stockP;
                
                // Calcular precio compra (55% del precio venta)
                $precioCompra = $producto->precioP * 0.55;
                $subtotal = $item['comprar'] * $precioCompra;
                $totalCompra += $subtotal;
                
                // Crear detalle de compra
                CompraDetalle::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['comprar'],
                    'created_at' => $carbonFecha,
                    'updated_at' => $carbonFecha,
                ]);
                
                // ACTUALIZAR STOCK (sumar la cantidad comprada)
                $producto->stockP += $item['comprar'];
                $producto->save();
                
                // Icono según necesidad
                $icono = ($stockAnterior <= 15) ? "⚠️" : "✓";
                
                echo "   {$icono} #{$producto->id} {$producto->descripcionP}: ";
                echo "Stock {$stockAnterior} → {$producto->stockP} (+{$item['comprar']})";
                
                if ($stockAnterior <= 15) {
                    echo " [Necesitaba compra]";
                }
                echo "\n";
                
                $productosEnEstaCompra++;
                $totalProductosComprados++;
            }
            
            // Si no se pudo comprar ningún producto
            if ($productosEnEstaCompra == 0) {
                $compra->delete();
                echo "   ❌ No se compraron productos\n\n";
                continue;
            }
            
            // Crear pago para esta compra
            if ($comprobante) {
                Pago::create([
                    'compra_id' => $compra->id,
                    'importe' => $totalCompra,
                    'vuelto' => 0,
                    'comprobante_id' => $comprobante->id,
                    'created_at' => $carbonFecha,
                    'updated_at' => $carbonFecha,
                ]);
            }
            
            echo "   📊 Productos comprados: {$productosEnEstaCompra}\n";
            echo "   💰 Total compra: S/ " . number_format($totalCompra, 2) . "\n\n";
            
            $totalCompras++;
        }
        
        // Estadísticas finales
        echo str_repeat("=", 60) . "\n";
        echo "✅ COMPRAS REALISTAS COMPLETADAS (4 sábados)\n";
        echo str_repeat("=", 60) . "\n";
        echo "📈 Estadísticas:\n";
        echo "   • Compras creadas: {$totalCompras}/4\n";
        echo "   • Total productos comprados: {$totalProductosComprados}\n";
        echo "   • Stock promedio: " . number_format(Producto::avg('stockP'), 1) . " unidades\n";
        
        // Mostrar stocks finales CON ALERTAS
        echo "\n📦 ESTADO DE STOCKS AL 31 DE OCTUBRE:\n";
        $productos = Producto::orderBy('stockP')->get();
        
        $bajoStock = 0;
        foreach ($productos as $producto) {
            $icono = "✓";
            $alerta = "";
            
            if ($producto->stockP <= 5) {
                $icono = "🔥";
                $alerta = " - STOCK CRÍTICO";
                $bajoStock++;
            } elseif ($producto->stockP <= 10) {
                $icono = "⚠️⚠️";
                $alerta = " - Muy bajo";
                $bajoStock++;
            } elseif ($producto->stockP <= 15) {
                $icono = "⚠️";
                $alerta = " - Bajo stock";
                $bajoStock++;
            }
            
            echo "   {$icono} #{$producto->id} {$producto->descripcionP}: ";
            echo "Stock {$producto->stockP}{$alerta}\n";
        }
        
        echo "\n🚨 PRODUCTOS CON STOCK ≤15 (aparecen en dashboard): {$bajoStock}/25\n";
        
        if ($bajoStock > 0) {
            echo "💡 La dueña verá {$bajoStock} productos que necesitan atención el sábado 1 de noviembre.\n";
        }
    }
}