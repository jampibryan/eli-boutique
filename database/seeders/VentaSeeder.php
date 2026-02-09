<?php

namespace Database\Seeders;

use App\Models\Caja;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Comprobante;
use App\Models\EstadoTransaccion;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\ProductoTallaStock;
use App\Models\Proveedor;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VentaSeeder extends Seeder
{
    // =====================================================================
    // OCTUBRE 2025 — 256 ventas + 4 compras en 25 días de operación
    // Ventas y compras se procesan cronológicamente para coherencia de stock
    // =====================================================================

    // Distribución de ventas por día [fecha, cantidadVentas]
    // Sem 1 (1-4 Oct): 70 ventas | Sem 2-4: ~52/semana | Sem 5: 30 ventas
    private $diasOperacion = [
        // Semana 1 — Inicio de mes activo (70 ventas)
        ['2025-10-01', 17], ['2025-10-02', 17], ['2025-10-03', 18], ['2025-10-04', 18],
        // Semana 2 — Actividad estable (52 ventas)
        ['2025-10-06', 8], ['2025-10-07', 9], ['2025-10-08', 9],
        ['2025-10-09', 9], ['2025-10-10', 8], ['2025-10-11', 9],
        // Semana 3 — Actividad estable (52 ventas)
        ['2025-10-13', 8], ['2025-10-14', 9], ['2025-10-15', 9],
        ['2025-10-16', 9], ['2025-10-17', 8], ['2025-10-18', 9],
        // Semana 4 — Actividad estable (52 ventas)
        ['2025-10-20', 8], ['2025-10-21', 9], ['2025-10-22', 9],
        ['2025-10-23', 8], ['2025-10-24', 9], ['2025-10-25', 9],
        // Última semana — Fin de mes tranquilo (30 ventas)
        ['2025-10-27', 10], ['2025-10-28', 10], ['2025-10-29', 10],
    ];

    // Sábados donde se hacen compras (después de ventas del día)
    private $diasCompra = ['2025-10-04', '2025-10-11', '2025-10-18', '2025-10-25'];

    // Peso de popularidad por producto (mayor = se vende más)
    private $productoPesos = [
        1 => 9, 2 => 8, 3 => 5, 4 => 5, 5 => 5,   // Polos & Camisetas
        6 => 8, 7 => 4, 8 => 4, 9 => 4, 10 => 4,   // Jeans & Pantalones
        11 => 2, 12 => 2, 13 => 2,                    // Shorts & Bermudas (baja rotación)
        14 => 3, 15 => 6, 16 => 4,                    // Abrigos & Chaquetas
        17 => 4, 18 => 3, 19 => 7,                    // Ropa Deportiva
    ];

    // Tallas válidas por producto (según categoría)
    // Cat 1,4,5 → tallas 1-4 (S,M,L,XL) | Cat 2,3 → tallas 5-8 (28,30,32,34)
    private $tallasProducto = [
        1 => [1,2,3,4], 2 => [1,2,3,4], 3 => [1,2,3,4], 4 => [1,2,3,4], 5 => [1,2,3,4],
        6 => [5,6,7,8], 7 => [5,6,7,8], 8 => [5,6,7,8], 9 => [5,6,7,8], 10 => [5,6,7,8],
        11 => [5,6,7,8], 12 => [5,6,7,8], 13 => [5,6,7,8],
        14 => [1,2,3,4], 15 => [1,2,3,4], 16 => [1,2,3,4],
        17 => [1,2,3,4], 18 => [1,2,3,4], 19 => [1,2,3,4],
    ];

    // Productos que NO se reabastecen en compras (baja rotación)
    private $sinRestock = [11, 12, 13];

    public function run()
    {
        mt_srand(2025); // Seed para resultados reproducibles

        echo "\n" . str_repeat("=", 60) . "\n";
        echo "  SEEDER OCTUBRE 2025 — 256 Ventas + 4 Compras\n";
        echo str_repeat("=", 60) . "\n\n";

        // Datos de referencia
        $estadoPendiente = EstadoTransaccion::where('descripcionET', 'Pendiente')->first();
        $estadoPagado = EstadoTransaccion::where('descripcionET', 'Pagado')->first();
        $estadoPagada = EstadoTransaccion::where('descripcionET', 'Pagada')->first();
        $comprobanteBoleta = Comprobante::where('descripcionCOM', 'Boleta')->first();
        $comprobanteFactura = Comprobante::where('descripcionCOM', 'Factura')->first();
        $clientes = Cliente::all();
        $proveedores = Proveedor::all();

        if ($clientes->isEmpty() || !$estadoPagado || !$estadoPendiente || !$comprobanteBoleta) {
            echo "❌ Faltan datos base (clientes, estados, comprobantes)\n";
            return;
        }

        $ventasTotales = 0;
        $comprasTotales = 0;
        $resumenDias = [];

        // ==================== PROCESAR OCTUBRE DÍA A DÍA ====================
        foreach ($this->diasOperacion as [$fecha, $numVentas]) {
            $carbonFecha = Carbon::parse($fecha);

            // 1. ABRIR CAJA
            $caja = Caja::create([
                'fecha' => $fecha,
                'clientesHoy' => 0,
                'productosVendidos' => 0,
                'ingresoDiario' => 0.00,
                'created_at' => $carbonFecha->copy()->setTime(8, 0),
                'updated_at' => $carbonFecha->copy()->setTime(8, 0),
            ]);

            echo "📅 {$fecha} ({$carbonFecha->locale('es')->dayName}) | Caja: {$caja->codigoCaja}\n";

            // 2. PROCESAR VENTAS DEL DÍA
            $productosVendidosDia = 0;

            for ($i = 0; $i < $numVentas; $i++) {
                $resultado = $this->crearVenta(
                    $fecha, $i, $numVentas, $caja,
                    $estadoPendiente, $estadoPagado,
                    $clientes, $comprobanteBoleta, $comprobanteFactura
                );

                if ($resultado > 0) {
                    $ventasTotales++;
                    $productosVendidosDia += $resultado;
                }
            }

            // Refrescar caja (los eventos de Venta la actualizaron)
            $caja->refresh();

            echo "   ✓ {$numVentas} ventas | {$caja->clientesHoy} clientes | ";
            echo "{$caja->productosVendidos} prod. | S/ " . number_format($caja->ingresoDiario, 2) . "\n";

            $resumenDias[$fecha] = [
                'ventas' => $numVentas,
                'ingreso' => $caja->ingresoDiario,
            ];

            // 3. SI ES SÁBADO → PROCESAR COMPRA
            if (in_array($fecha, $this->diasCompra)) {
                $this->crearCompra($fecha, $proveedores, $estadoPagada, $comprobanteFactura);
                $comprasTotales++;
            }

            echo "\n";
        }

        // ==================== ESTADÍSTICAS FINALES ====================
        $this->mostrarEstadisticas($ventasTotales, $comprasTotales, $resumenDias);
    }

    // =====================================================================
    // CREAR UNA VENTA
    // =====================================================================
    private function crearVenta($fecha, $indice, $totalDia, $caja, $estadoPendiente, $estadoPagado, $clientes, $comprobanteBoleta, $comprobanteFactura)
    {
        // Hora realista: 60% mañana (8-12), 40% tarde (14-17)
        $esMañana = ($indice / max($totalDia, 1)) < 0.6;
        $hora = $esMañana ? mt_rand(8, 12) : mt_rand(14, 17);
        $minuto = mt_rand(0, 59);
        $fechaCompleta = Carbon::parse($fecha)->setTime($hora, $minuto, mt_rand(0, 59));

        // Cliente aleatorio
        $cliente = $clientes[mt_rand(0, $clientes->count() - 1)];

        // Comprobante: 75% Boleta, 25% Factura
        $comprobante = mt_rand(1, 100) <= 75 ? $comprobanteBoleta : $comprobanteFactura;

        // Cantidad de productos en esta venta: 1 (65%), 2 (30%), 3 (5%)
        $numProductos = $this->randomPonderado([1 => 65, 2 => 30, 3 => 5]);

        // Seleccionar productos con stock disponible
        $detallesData = [];
        $productosUsados = [];
        $montoTotal = 0;

        for ($p = 0; $p < $numProductos; $p++) {
            $intentos = 0;
            $productoId = null;
            $tallaId = null;
            $stockDisponible = 0;

            while ($intentos < 15) {
                $candidato = $this->randomPonderado($this->productoPesos);

                if (in_array($candidato, $productosUsados)) {
                    $intentos++;
                    continue;
                }

                $tallaInfo = $this->obtenerTallaConStock($candidato);
                if ($tallaInfo) {
                    $productoId = $candidato;
                    $tallaId = $tallaInfo['talla_id'];
                    $stockDisponible = $tallaInfo['stock'];
                    break;
                }
                $intentos++;
            }

            if (!$productoId) continue;

            $productosUsados[] = $productoId;
            $producto = Producto::find($productoId);

            // Cantidad: 1-2 normal, hasta 3 si es popular y hay stock
            $maxCant = min($stockDisponible, $this->productoPesos[$productoId] >= 7 ? 3 : 2);
            $cantidad = $this->randomPonderado([1 => 55, 2 => 35, 3 => 10]);
            $cantidad = max(1, min($cantidad, $maxCant));

            $precioUnitario = $producto->precioP;
            $baseImponible = round($precioUnitario / 1.18, 2);
            $igvUnit = round($precioUnitario - $baseImponible, 2);
            $subtotalLinea = round($cantidad * $precioUnitario, 2);

            $detallesData[] = [
                'producto_id' => $productoId,
                'talla_id' => $tallaId,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'base_imponible' => $baseImponible,
                'igv' => $igvUnit,
                'subtotal' => $subtotalLinea,
            ];

            $montoTotal += $subtotalLinea;
        }

        if (empty($detallesData) || $montoTotal == 0) return 0;

        // Totales de la venta (precio incluye IGV)
        $subTotal = round($montoTotal / 1.18, 2);
        $igv = round($montoTotal - $subTotal, 2);

        // Paso 1: Crear venta con estado PENDIENTE (no sincroniza caja aún)
        $venta = Venta::create([
            'caja_id' => $caja->id,
            'cliente_id' => $cliente->id,
            'estado_transaccion_id' => $estadoPendiente->id,
            'subTotal' => $subTotal,
            'IGV' => $igv,
            'montoTotal' => round($montoTotal, 2),
            'created_at' => $fechaCompleta,
            'updated_at' => $fechaCompleta,
        ]);

        // Paso 2: Crear detalles + decrementar stock por talla
        $totalProductosVenta = 0;

        foreach ($detallesData as $det) {
            VentaDetalle::create([
                'venta_id' => $venta->id,
                'producto_id' => $det['producto_id'],
                'cantidad' => $det['cantidad'],
                'precio_unitario' => $det['precio_unitario'],
                'base_imponible' => $det['base_imponible'],
                'igv' => $det['igv'],
                'subtotal' => $det['subtotal'],
                'created_at' => $fechaCompleta,
                'updated_at' => $fechaCompleta,
            ]);

            // Decrementar stock de la talla vendida
            ProductoTallaStock::where('producto_id', $det['producto_id'])
                ->where('producto_talla_id', $det['talla_id'])
                ->decrement('stock', $det['cantidad']);

            $totalProductosVenta += $det['cantidad'];
        }

        // Paso 3: Crear pago
        Pago::create([
            'venta_id' => $venta->id,
            'importe' => round($montoTotal, 2),
            'vuelto' => 0,
            'comprobante_id' => $comprobante->id,
            'created_at' => $fechaCompleta,
            'updated_at' => $fechaCompleta,
        ]);

        // Paso 4: Cambiar a PAGADO → evento 'updated' sincroniza la caja
        // (clientesHoy +1, ingresoDiario +monto, productosVendidos +cantidad)
        $venta->estado_transaccion_id = $estadoPagado->id;
        $venta->timestamps = false;
        $venta->save();

        return $totalProductosVenta;
    }

    // =====================================================================
    // CREAR COMPRA ESTRATÉGICA (sábados por la tarde)
    // =====================================================================
    private function crearCompra($fecha, $proveedores, $estadoPagada, $comprobanteFactura)
    {
        $carbonFecha = Carbon::parse($fecha)->setTime(17, mt_rand(0, 30));
        $proveedor = $proveedores[mt_rand(0, $proveedores->count() - 1)];

        echo "   🛒 COMPRA {$fecha} (reposición de stock):\n";

        // Identificar productos/tallas que necesitan reabastecimiento
        $productosAComprar = [];

        foreach ($this->tallasProducto as $productoId => $tallas) {
            // No reabastecer productos de baja rotación
            if (in_array($productoId, $this->sinRestock)) continue;

            $stockTotal = ProductoTallaStock::where('producto_id', $productoId)->sum('stock');
            $esPopular = $this->productoPesos[$productoId] >= 7;

            // Criterio conservador: solo reponer cuando stock es crítico
            $necesitaCompra = $stockTotal <= 10 || ($esPopular && $stockTotal <= 16);

            if (!$necesitaCompra) continue;

            foreach ($tallas as $tallaId) {
                $stockTalla = ProductoTallaStock::where('producto_id', $productoId)
                    ->where('producto_talla_id', $tallaId)
                    ->value('stock') ?? 0;

                // Solo comprar si la talla tiene stock muy bajo
                if ($stockTalla <= 3) {
                    $cantCompra = $esPopular ? mt_rand(4, 8) : mt_rand(3, 5);
                    $productosAComprar[] = [
                        'producto_id' => $productoId,
                        'talla_id' => $tallaId,
                        'cantidad' => $cantCompra,
                        'stock_actual' => $stockTalla,
                    ];
                }
            }
        }

        if (empty($productosAComprar)) {
            // Compra preventiva: reabastecer los 3 productos más populares
            $populares = [1, 6, 19]; // Top 3 populares
            foreach ($populares as $productoId) {
                $tallas = $this->tallasProducto[$productoId];
                $tallaId = $tallas[mt_rand(0, count($tallas) - 1)];
                $productosAComprar[] = [
                    'producto_id' => $productoId,
                    'talla_id' => $tallaId,
                    'cantidad' => mt_rand(3, 5),
                    'stock_actual' => 0,
                ];
            }
        }

        // Crear registro de compra
        $compra = Compra::create([
            'proveedor_id' => $proveedor->id,
            'comprobante_id' => $comprobanteFactura->id,
            'estado_transaccion_id' => $estadoPagada->id,
            'subtotal' => 0,
            'descuento' => 0,
            'igv' => 0,
            'total' => 0,
            'created_at' => $carbonFecha,
            'updated_at' => $carbonFecha,
        ]);

        $totalCompra = 0;
        $unidadesTotales = 0;
        $productosResumen = [];

        foreach ($productosAComprar as $item) {
            $producto = Producto::find($item['producto_id']);
            $precioCompra = round($producto->precioP * 0.55, 2);
            $subtotalLinea = round($item['cantidad'] * $precioCompra, 2);

            CompraDetalle::create([
                'compra_id' => $compra->id,
                'producto_id' => $item['producto_id'],
                'producto_talla_id' => $item['talla_id'],
                'cantidad' => $item['cantidad'],
                'precio_final' => $precioCompra,
                'subtotal_linea' => $subtotalLinea,
                'created_at' => $carbonFecha,
                'updated_at' => $carbonFecha,
            ]);

            // Incrementar stock de la talla
            ProductoTallaStock::where('producto_id', $item['producto_id'])
                ->where('producto_talla_id', $item['talla_id'])
                ->increment('stock', $item['cantidad']);

            $totalCompra += $subtotalLinea;
            $unidadesTotales += $item['cantidad'];

            // Acumular resumen por producto
            $key = $item['producto_id'];
            if (!isset($productosResumen[$key])) {
                $productosResumen[$key] = ['nombre' => $producto->descripcionP, 'cant' => 0];
            }
            $productosResumen[$key]['cant'] += $item['cantidad'];
        }

        // Actualizar totales financieros de la compra
        $igvCompra = round($totalCompra * 0.18, 2);
        $compra->timestamps = false;
        $compra->update([
            'subtotal' => round($totalCompra, 2),
            'igv' => $igvCompra,
            'total' => round($totalCompra + $igvCompra, 2),
        ]);

        // Crear pago de la compra
        Pago::create([
            'compra_id' => $compra->id,
            'importe' => round($totalCompra + $igvCompra, 2),
            'vuelto' => 0,
            'comprobante_id' => $comprobanteFactura->id,
            'created_at' => $carbonFecha,
            'updated_at' => $carbonFecha,
        ]);

        // Mostrar resumen
        echo "      {$compra->codigoCompra}: ";
        echo count($productosResumen) . " productos, {$unidadesTotales} unidades, ";
        echo "S/ " . number_format($totalCompra + $igvCompra, 2) . "\n";

        foreach ($productosResumen as $id => $info) {
            echo "        • #{$id} {$info['nombre']}: +{$info['cant']} u.\n";
        }
    }

    // =====================================================================
    // ESTADÍSTICAS FINALES
    // =====================================================================
    private function mostrarEstadisticas($ventasTotales, $comprasTotales, $resumenDias)
    {
        echo str_repeat("=", 60) . "\n";
        echo "  ✅ OCTUBRE 2025 COMPLETADO\n";
        echo str_repeat("=", 60) . "\n\n";

        $totalIngresos = array_sum(array_column($resumenDias, 'ingreso'));
        echo "📊 RESUMEN GENERAL:\n";
        echo "   • Ventas creadas: {$ventasTotales}/256\n";
        echo "   • Compras creadas: {$comprasTotales}/4\n";
        echo "   • Cajas creadas: " . count($resumenDias) . " días\n";
        echo "   • Ingreso total: S/ " . number_format($totalIngresos, 2) . "\n\n";

        // Top 5 mejores días
        uasort($resumenDias, fn($a, $b) => $b['ingreso'] <=> $a['ingreso']);
        echo "🏆 TOP 5 DÍAS CON MAYOR INGRESO:\n";
        $count = 0;
        foreach ($resumenDias as $fecha => $stats) {
            if ($count++ >= 5) break;
            echo "   {$fecha}: S/ " . number_format($stats['ingreso'], 2) . " ({$stats['ventas']} ventas)\n";
        }

        // Estado de stocks finales
        echo "\n📦 ESTADO DE STOCKS AL 29/10/2025:\n";
        $productos = Producto::orderBy('id')->get();
        $bajoStock = 0;

        foreach ($productos as $producto) {
            $stock = $producto->stockTotal;
            $alerta = '';

            if ($stock <= 5) {
                $icono = '🔥';
                $alerta = ' — CRÍTICO';
                $bajoStock++;
            } elseif ($stock <= 10) {
                $icono = '⚠️⚠️';
                $alerta = ' — Muy bajo';
                $bajoStock++;
            } elseif ($stock <= 15) {
                $icono = '⚠️';
                $alerta = ' — Bajo';
                $bajoStock++;
            } else {
                $icono = '✓';
            }

            echo "   {$icono} #{$producto->id} {$producto->descripcionP}: {$stock} u.{$alerta}\n";
        }

        echo "\n🚨 Productos con stock ≤15: {$bajoStock}/19\n";

        // Comprobantes
        $boletas = Pago::whereNotNull('venta_id')
            ->whereHas('comprobante', fn($q) => $q->where('descripcionCOM', 'Boleta'))
            ->count();
        $facturas = Pago::whereNotNull('venta_id')
            ->whereHas('comprobante', fn($q) => $q->where('descripcionCOM', 'Factura'))
            ->count();

        echo "\n🧾 COMPROBANTES:\n";
        echo "   • Boletas: {$boletas}\n";
        echo "   • Facturas: {$facturas}\n";
    }

    // =====================================================================
    // UTILIDADES
    // =====================================================================

    /**
     * Obtener una talla con stock disponible para un producto.
     */
    private function obtenerTallaConStock($productoId)
    {
        $tallas = $this->tallasProducto[$productoId] ?? [];
        $tallasShuffled = $tallas;
        shuffle($tallasShuffled);

        foreach ($tallasShuffled as $tallaId) {
            $stock = ProductoTallaStock::where('producto_id', $productoId)
                ->where('producto_talla_id', $tallaId)
                ->value('stock');

            if ($stock && $stock > 0) {
                return ['talla_id' => $tallaId, 'stock' => $stock];
            }
        }

        return null;
    }

    /**
     * Selección aleatoria ponderada: devuelve la clave según peso.
     * Ej: [1 => 65, 2 => 30, 3 => 5] → 65% retorna 1, 30% retorna 2, 5% retorna 3
     */
    private function randomPonderado(array $pesos)
    {
        $total = array_sum($pesos);
        $rand = mt_rand(1, $total);
        $acumulado = 0;

        foreach ($pesos as $valor => $peso) {
            $acumulado += $peso;
            if ($rand <= $acumulado) return $valor;
        }

        return array_key_first($pesos);
    }
}
