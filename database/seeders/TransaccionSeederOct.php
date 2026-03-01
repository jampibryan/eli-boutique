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

class TransaccionSeederOct extends Seeder
{
    // =====================================================================
    // OCTUBRE 2025 — 256 ventas + 50 compras | 27 dias operativos
    // - Compras: miercoles y sabados (NO el primer dia, sin repetir proveedor)
    // - Ventas: lunes a sabado
    // - Horas secuenciales con minutos/segundos aleatorios
    // - Proveedores especializados por categoria (1 prov = 1 cat)
    // - Stock coherente: algunos productos terminan con stock <= 15
    // =====================================================================

    // [fecha, numVentas, numCompras]
    // sum(ventas)=256, sum(compras)=50
    // Compras en: Mi 8, Sa 4, Sa 11, Mi 15, Vi 17, Sa 18, Mi 22, Sa 25, Mi 29, Vi 31
    // (NO Oct 1 — primer dia sin compras)
    private $diasOperacion = [
        // Semana 1 (Mi 1 - Sa 4) — Solo ventas el primer dia
        ['2025-10-01', 12, 0],   // Miercoles — INAUGURACION, sin compras
        ['2025-10-02', 11, 0],   // Jueves
        ['2025-10-03', 12, 0],   // Viernes
        ['2025-10-04', 13, 5],   // Sabado — primera reposicion
        // Semana 2 (Lu 6 - Sa 11)
        ['2025-10-06', 9, 0],    // Lunes
        ['2025-10-07', 10, 0],   // Martes
        ['2025-10-08', 9, 5],    // Miercoles
        ['2025-10-09', 10, 0],   // Jueves
        ['2025-10-10', 9, 3],    // Viernes
        ['2025-10-11', 11, 5],   // Sabado
        // Semana 3 (Lu 13 - Sa 18)
        ['2025-10-13', 8, 0],    // Lunes
        ['2025-10-14', 9, 0],    // Martes
        ['2025-10-15', 10, 4],   // Miercoles
        ['2025-10-16', 9, 0],    // Jueves
        ['2025-10-17', 8, 3],    // Viernes
        ['2025-10-18', 10, 5],   // Sabado
        // Semana 4 (Lu 20 - Sa 25)
        ['2025-10-20', 9, 0],    // Lunes
        ['2025-10-21', 9, 0],    // Martes
        ['2025-10-22', 10, 4],   // Miercoles
        ['2025-10-23', 8, 0],    // Jueves
        ['2025-10-24', 9, 3],    // Viernes
        ['2025-10-25', 10, 5],   // Sabado
        // Semana 5 (Lu 27 - Vi 31)
        ['2025-10-27', 8, 0],    // Lunes
        ['2025-10-28', 9, 0],    // Martes
        ['2025-10-29', 8, 4],    // Miercoles
        ['2025-10-30', 8, 0],    // Jueves
        ['2025-10-31', 8, 4],    // Viernes
    ];

    // Proveedor ID => Productos (1 proveedor = 1 categoria, nunca se mezclan)
    private $proveedorProductos = [
        1 => [1, 2, 3, 4, 5],       // Moda Eclipse => Polos & Camisetas
        2 => [6, 7, 8, 9, 10],      // Estilo Urbano => Jeans & Pantalones
        3 => [11, 12, 13],           // Hilos de Plata => Shorts & Bermudas
        4 => [14, 15, 16],           // Ropa Estelar => Abrigos & Chaquetas
        5 => [17, 18, 19],           // Textiles de Oro => Ropa Deportiva
    ];

    // Popularidad de venta (mayor = se vende mas)
    private $productoPesos = [
        1 => 7, 2 => 7, 3 => 6, 4 => 5, 5 => 5,    // Polos
        6 => 5, 7 => 4, 8 => 4, 9 => 3, 10 => 3,     // Jeans
        11 => 3, 12 => 3, 13 => 2,                      // Shorts
        14 => 3, 15 => 6, 16 => 4,                      // Abrigos
        17 => 4, 18 => 5, 19 => 7,                      // Deportiva
    ];

    // Tallas por producto
    private $tallasProducto = [
        1 => [1,2,3,4], 2 => [1,2,3,4], 3 => [1,2,3,4], 4 => [1,2,3,4], 5 => [1,2,3,4],
        6 => [5,6,7,8], 7 => [5,6,7,8], 8 => [5,6,7,8], 9 => [5,6,7,8], 10 => [5,6,7,8],
        11 => [5,6,7,8], 12 => [5,6,7,8], 13 => [5,6,7,8],
        14 => [1,2,3,4], 15 => [1,2,3,4], 16 => [1,2,3,4],
        17 => [1,2,3,4], 18 => [1,2,3,4], 19 => [1,2,3,4],
    ];

    // Productos con reposicion REDUCIDA => terminan con stock 10-15
    // Objetivo POR TALLA para compra reactiva (menor valor = menos reposición)
    private $stockBajo = [1, 2, 6, 15, 19];
    private $objetivoPorProducto = [
        1 => 4,   // Polo clásico: reponer a 4/talla
        2 => 3,   // Camiseta básica: reponer a 3/talla
        6 => 3,   // Jeans skinny: reponer a 3/talla
        15 => 3,  // Chaqueta bomber: reponer a 3/talla
        19 => 3,  // Sudadera: reponer a 3/talla
    ];

    // =====================================================================
    // METODO PRINCIPAL
    // =====================================================================
    public function run()
    {
        mt_srand(2025);

        echo "\n" . str_repeat("=", 70) . "\n";
        echo "  ELI BOUTIQUE - SEEDER OCTUBRE 2025\n";
        echo "  256 Ventas + 50 Compras | 27 dias operativos\n";
        echo str_repeat("=", 70) . "\n\n";

        $estadoPendiente = EstadoTransaccion::where('descripcionET', 'Pendiente')->first();
        $estadoPagado = EstadoTransaccion::where('descripcionET', 'Pagado')->first();
        $estadoPagada = EstadoTransaccion::where('descripcionET', 'Pagada')->first();
        $comprobanteBoleta = Comprobante::where('descripcionCOM', 'Boleta')->first();
        $comprobanteFactura = Comprobante::where('descripcionCOM', 'Factura')->first();
        $clientes = Cliente::all();

        if ($clientes->isEmpty() || !$estadoPagado || !$estadoPendiente || !$comprobanteBoleta) {
            echo "ERROR: Faltan datos base (clientes, estados o comprobantes)\n";
            return;
        }

        $ventasTotales = 0;
        $comprasTotales = 0;
        $resumenDias = [];

        // ==================== PROCESAR DIA A DIA ====================
        foreach ($this->diasOperacion as [$fecha, $numVentas, $numCompras]) {
            $carbonFecha = Carbon::parse($fecha);
            $diaSemana = ucfirst($carbonFecha->locale('es')->dayName);
            $totalTransacciones = $numVentas + $numCompras;

            // 1. ABRIR CAJA
            $caja = Caja::create([
                'fecha' => $fecha,
                'clientesHoy' => 0,
                'productosVendidos' => 0,
                'ingresoDiario' => 0.00,
                'egresoDiario' => 0.00,
                'created_at' => $carbonFecha->copy()->setTime(8, mt_rand(0, 4), mt_rand(10, 59)),
                'updated_at' => $carbonFecha->copy()->setTime(8, mt_rand(0, 4), mt_rand(10, 59)),
            ]);

            echo "  " . str_repeat("-", 66) . "\n";
            echo "  {$fecha} ({$diaSemana}) | Caja #{$caja->codigoCaja}\n";

            // 2. GENERAR HORAS SECUENCIALES
            $horas = $this->generarHorasSecuenciales($carbonFecha, $totalTransacciones);

            $horasCompras = array_slice($horas, 0, $numCompras);
            $horasVentas = array_slice($horas, $numCompras);

            // 3. COMPRAS PRIMERO (sin repetir proveedor en el mismo dia)
            if ($numCompras > 0) {
                $proveedoresUsadosHoy = [];

                for ($c = 0; $c < $numCompras; $c++) {
                    $this->crearCompra(
                        $horasCompras[$c], $estadoPagada,
                        $comprobanteFactura, $caja, $proveedoresUsadosHoy
                    );
                    $comprasTotales++;
                }
                $caja->refresh();

                $horaIni = $this->horaAmPm($horasCompras[0]);
                $horaFin = $this->horaAmPm($horasCompras[$numCompras - 1]);
                echo "    Compras: {$numCompras} ({$horaIni} - {$horaFin})";
                echo " | Egreso: S/ " . number_format($caja->egresoDiario, 2) . "\n";
            }

            // 4. VENTAS
            for ($v = 0; $v < $numVentas; $v++) {
                $resultado = $this->crearVenta(
                    $horasVentas[$v], $caja,
                    $estadoPendiente, $estadoPagado,
                    $clientes, $comprobanteBoleta, $comprobanteFactura
                );
                if ($resultado > 0) {
                    $ventasTotales++;
                }
            }

            $caja->refresh();

            $horaIniV = $this->horaAmPm($horasVentas[0]);
            $horaFinV = $this->horaAmPm($horasVentas[$numVentas - 1]);
            echo "    Ventas:  {$numVentas} ({$horaIniV} - {$horaFinV})";
            echo " | {$caja->clientesHoy} clientes | {$caja->productosVendidos} prod.";
            echo " | Ingreso: S/ " . number_format($caja->ingresoDiario, 2);
            if ($caja->egresoDiario > 0) {
                echo " | Balance: S/ " . number_format($caja->balanceDiario, 2);
            }
            echo "\n";

            $resumenDias[$fecha] = [
                'dia' => $diaSemana,
                'ventas' => $numVentas,
                'compras' => $numCompras,
                'ingreso' => $caja->ingresoDiario,
                'egreso' => $caja->egresoDiario,
            ];
        }

        echo "\n";

        // AJUSTE FINAL: corregir stock para que stockBajo quede 10-15 y normal 20-45
        $this->ajustarStockFinal();

        $this->validarCoherencia();
        $this->mostrarEstadisticas($ventasTotales, $comprasTotales, $resumenDias);
    }

    // =====================================================================
    // AJUSTE FINAL DE STOCK
    // Modifica cantidades de CompraDetalle existentes para que el stock
    // aterrice en los rangos objetivo. Recalcula totales asociados.
    // =====================================================================
    private function ajustarStockFinal()
    {
        echo "  AJUSTE FINAL DE STOCK:\n";
        $productos = Producto::orderBy('id')->get();

        foreach ($productos as $producto) {
            $stockActual = $producto->stockTotal;
            $esStockBajo = in_array($producto->id, $this->stockBajo);
            $minTarget = $esStockBajo ? 10 : 20;
            $maxTarget = $esStockBajo ? 15 : 45;

            if ($stockActual >= $minTarget && $stockActual <= $maxTarget) continue;

            // Calcular stock objetivo (mitad del rango)
            $objetivo = $esStockBajo ? mt_rand(11, 14) : mt_rand(25, 38);
            $delta = $objetivo - $stockActual; // positivo = necesita más, negativo = necesita menos

            if ($delta == 0) continue;

            if ($delta > 0) {
                // NECESITA MAS STOCK: buscar CompraDetalle existente y aumentar cantidad
                $detalle = CompraDetalle::where('producto_id', $producto->id)
                    ->orderBy('id', 'desc')->first();

                if ($detalle) {
                    $detalle->cantidad += $delta;
                    $detalle->subtotal_linea = round($detalle->cantidad * $detalle->precio_final, 2);
                    $detalle->save();

                    // Incrementar stock en la talla correspondiente
                    ProductoTallaStock::where('producto_id', $producto->id)
                        ->where('producto_talla_id', $detalle->producto_talla_id)
                        ->increment('stock', $delta);

                    // Recalcular totales de la compra
                    $this->recalcularCompra($detalle->compra_id);
                    echo "    #{$producto->id} {$producto->descripcionP}: {$stockActual} -> {$objetivo} (+{$delta} via compra)\n";
                }
            } else {
                // STOCK DEMASIADO ALTO: buscar CompraDetalle existente y reducir cantidad
                $reducir = abs($delta);
                $detalles = CompraDetalle::where('producto_id', $producto->id)
                    ->orderBy('id', 'desc')->get();

                foreach ($detalles as $detalle) {
                    if ($reducir <= 0) break;
                    $maxReduccion = $detalle->cantidad - 1; // mantener al menos 1

                    // Verificar stock disponible en esa talla para no ir a negativo
                    $stockTalla = ProductoTallaStock::where('producto_id', $producto->id)
                        ->where('producto_talla_id', $detalle->producto_talla_id)->value('stock') ?? 0;
                    $maxReduccion = min($maxReduccion, $stockTalla);
                    $reduccionReal = min($reducir, $maxReduccion);

                    if ($reduccionReal > 0) {
                        $detalle->cantidad -= $reduccionReal;
                        $detalle->subtotal_linea = round($detalle->cantidad * $detalle->precio_final, 2);
                        $detalle->save();

                        ProductoTallaStock::where('producto_id', $producto->id)
                            ->where('producto_talla_id', $detalle->producto_talla_id)
                            ->decrement('stock', $reduccionReal);

                        $this->recalcularCompra($detalle->compra_id);
                        $reducir -= $reduccionReal;
                    }
                }
                $stockFinal = $objetivo + $reducir; // si no se pudo reducir todo
                echo "    #{$producto->id} {$producto->descripcionP}: {$stockActual} -> {$stockFinal} (-" . (abs($delta) - $reducir) . " via compra)\n";
            }
        }
        echo "\n";
    }

    // Recalcular subtotal, igv, total de una compra y su pago/caja
    private function recalcularCompra(int $compraId)
    {
        $compra = Compra::find($compraId);
        $subtotal = CompraDetalle::where('compra_id', $compraId)->sum('subtotal_linea');
        $igv = round($subtotal * 0.18, 2);
        $total = round($subtotal + $igv, 2);
        $oldTotal = $compra->total;

        $compra->timestamps = false;
        $compra->update(['subtotal' => round($subtotal, 2), 'igv' => $igv, 'total' => $total]);

        // Actualizar pago
        $pago = Pago::where('compra_id', $compraId)->first();
        if ($pago) {
            $pago->importe = $total;
            $pago->save();
        }

        // Actualizar egreso en caja
        $fechaCompra = $compra->created_at instanceof Carbon
            ? $compra->created_at->format('Y-m-d')
            : Carbon::parse($compra->created_at)->format('Y-m-d');
        $caja = Caja::where('fecha', $fechaCompra)->first();
        if ($caja) {
            $caja->egresoDiario += ($total - $oldTotal);
            $caja->save();
        }
    }

    // =====================================================================
    // GENERAR HORAS SECUENCIALES
    // N timestamps entre ~8:06 AM y ~6:47 PM, siempre crecientes
    // =====================================================================
    private function generarHorasSecuenciales(Carbon $fecha, int $total): array
    {
        $inicioSeg = 8 * 3600 + 6 * 60;    // 08:06:00
        $finSeg    = 18 * 3600 + 47 * 60;  // 18:47:00
        $rangoTotal = $finSeg - $inicioSeg;

        $horas = [];
        $slotSize = intdiv($rangoTotal, $total);

        for ($i = 0; $i < $total; $i++) {
            $slotInicio = $inicioSeg + ($i * $slotSize);
            $slotFin = $slotInicio + $slotSize - 30;

            $segundosDia = mt_rand($slotInicio, max($slotInicio, $slotFin));

            $h = intdiv($segundosDia, 3600);
            $m = intdiv($segundosDia % 3600, 60);
            $s = $segundosDia % 60;

            $horas[] = $fecha->copy()->setTime($h, $m, $s);
        }

        return $horas;
    }

    // =====================================================================
    // CREAR UNA COMPRA
    // - Estado final: Pagada (con fechas retroactivas)
    // - No repite proveedor en el mismo dia
    // - No reabastece productos en $sinRestock
    // - Cantidades conservadoras: 2-3 unidades por talla
    // =====================================================================
    private function crearCompra(Carbon $fechaHora, $estadoPagada, $comprobanteFactura, $caja, array &$proveedoresUsadosHoy)
    {
        // Seleccionar proveedor que NO se haya usado hoy
        $proveedorId = $this->seleccionarProveedorPorNecesidad($proveedoresUsadosHoy);
        $proveedoresUsadosHoy[] = $proveedorId;

        $proveedor = Proveedor::find($proveedorId);
        $productosDelProveedor = $this->proveedorProductos[$proveedorId];

        // Todos los productos del proveedor son reabastecibles
        $productosReabastecibles = array_values($productosDelProveedor);

        // 2-3 items por compra
        $maxItems = min(3, count($productosReabastecibles));
        $numItems = mt_rand(2, max(2, $maxItems));

        $productosOrdenados = $this->ordenarPorStockAscendente($productosReabastecibles);
        $productosAComprar = array_slice($productosOrdenados, 0, $numItems);

        // Fechas retroactivas
        $diasAntes = mt_rand(4, 7);
        $fechaEnvio = $fechaHora->copy()->subDays($diasAntes)->format('Y-m-d');
        $fechaCotizacion = $fechaHora->copy()->subDays($diasAntes - mt_rand(1, 2))->format('Y-m-d');
        $fechaAprobacion = $fechaHora->copy()->subDays(mt_rand(1, 2))->format('Y-m-d');

        $compra = Compra::create([
            'proveedor_id' => $proveedor->id,
            'comprobante_id' => $comprobanteFactura->id,
            'estado_transaccion_id' => $estadoPagada->id,
            'fecha_envio' => $fechaEnvio,
            'fecha_cotizacion' => $fechaCotizacion,
            'fecha_aprobacion' => $fechaAprobacion,
            'fecha_entrega_estimada' => $fechaHora->format('Y-m-d'),
            'condiciones_pago' => 'Pago contra entrega',
            'subtotal' => 0,
            'descuento' => 0,
            'igv' => 0,
            'total' => 0,
            'created_at' => $fechaHora,
            'updated_at' => $fechaHora,
        ]);

        $subtotalCompra = 0;

        foreach ($productosAComprar as $productoId) {
            $producto = Producto::find($productoId);
            $tallas = $this->tallasProducto[$productoId];

            // Solo 1 talla por producto (compra conservadora)
            $tallasShuffled = $tallas;
            shuffle($tallasShuffled);

            // Elegir la talla con menor stock
            $mejorTalla = null;
            $menorStock = PHP_INT_MAX;
            foreach ($tallasShuffled as $tid) {
                $st = ProductoTallaStock::where('producto_id', $productoId)
                    ->where('producto_talla_id', $tid)->value('stock') ?? 0;
                if ($st < $menorStock) {
                    $menorStock = $st;
                    $mejorTalla = $tid;
                }
            }

            $tallaId = $mejorTalla ?? $tallasShuffled[0];

            // Calcular cantidad segun stock actual vs objetivo
            $stockActual = ProductoTallaStock::where('producto_id', $productoId)
                ->where('producto_talla_id', $tallaId)->value('stock') ?? 0;

            // Objetivo por talla: personalizado para stockBajo, 9 para normal
            $objetivoTalla = $this->objetivoPorProducto[$productoId] ?? 9;
            $deficit = max(0, $objetivoTalla - $stockActual);

            // Comprar entre 1 y deficit+1, minimo 1, maximo 8
            $cantidad = max(1, min($deficit + mt_rand(0, 1), 8));
            $precioCompra = round($producto->precioP * 0.55, 2);
            $subtotalLinea = round($cantidad * $precioCompra, 2);

            CompraDetalle::create([
                'compra_id' => $compra->id,
                'producto_id' => $productoId,
                'producto_talla_id' => $tallaId,
                'cantidad' => $cantidad,
                'precio_cotizado' => $precioCompra,
                'precio_final' => $precioCompra,
                'subtotal_linea' => $subtotalLinea,
                'created_at' => $fechaHora,
                'updated_at' => $fechaHora,
            ]);

            ProductoTallaStock::where('producto_id', $productoId)
                ->where('producto_talla_id', $tallaId)
                ->increment('stock', $cantidad);

            $subtotalCompra += $subtotalLinea;
        }

        $igvCompra = round($subtotalCompra * 0.18, 2);
        $totalCompra = round($subtotalCompra + $igvCompra, 2);

        $compra->timestamps = false;
        $compra->update([
            'subtotal' => round($subtotalCompra, 2),
            'igv' => $igvCompra,
            'total' => $totalCompra,
        ]);

        Pago::create([
            'compra_id' => $compra->id,
            'importe' => $totalCompra,
            'vuelto' => 0,
            'comprobante_id' => $comprobanteFactura->id,
            'created_at' => $fechaHora,
            'updated_at' => $fechaHora,
        ]);

        $caja->increment('egresoDiario', $totalCompra);
    }

    // =====================================================================
    // CREAR UNA VENTA
    // =====================================================================
    private function crearVenta(Carbon $fechaHora, $caja, $estadoPendiente, $estadoPagado, $clientes, $comprobanteBoleta, $comprobanteFactura)
    {
        $cliente = $clientes[mt_rand(0, $clientes->count() - 1)];
        $comprobante = mt_rand(1, 100) <= 75 ? $comprobanteBoleta : $comprobanteFactura;

        // Cantidad de productos: 1 (60%), 2 (32%), 3 (8%)
        $numProductos = $this->randomPonderado([1 => 60, 2 => 32, 3 => 8]);

        $detallesData = [];
        $productosUsados = [];
        $montoTotal = 0;

        for ($p = 0; $p < $numProductos; $p++) {
            $intentos = 0;
            $productoId = null;
            $tallaId = null;
            $stockDisponible = 0;

            while ($intentos < 20) {
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

            $maxCant = min($stockDisponible, $this->productoPesos[$productoId] >= 7 ? 3 : 2);
            $cantidad = $this->randomPonderado([1 => 50, 2 => 35, 3 => 15]);
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

        $subTotal = round($montoTotal / 1.18, 2);
        $igv = round($montoTotal - $subTotal, 2);

        $venta = Venta::create([
            'caja_id' => $caja->id,
            'cliente_id' => $cliente->id,
            'estado_transaccion_id' => $estadoPendiente->id,
            'subTotal' => $subTotal,
            'IGV' => $igv,
            'montoTotal' => round($montoTotal, 2),
            'created_at' => $fechaHora,
            'updated_at' => $fechaHora,
        ]);

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
                'created_at' => $fechaHora,
                'updated_at' => $fechaHora,
            ]);

            ProductoTallaStock::where('producto_id', $det['producto_id'])
                ->where('producto_talla_id', $det['talla_id'])
                ->decrement('stock', $det['cantidad']);

            $totalProductosVenta += $det['cantidad'];
        }

        Pago::create([
            'venta_id' => $venta->id,
            'importe' => round($montoTotal, 2),
            'vuelto' => 0,
            'comprobante_id' => $comprobante->id,
            'created_at' => $fechaHora,
            'updated_at' => $fechaHora,
        ]);

        // Cambiar a PAGADO => evento updated sincroniza caja
        $venta->estado_transaccion_id = $estadoPagado->id;
        $venta->timestamps = false;
        $venta->save();

        return $totalProductosVenta;
    }

    // =====================================================================
    // SELECCIONAR PROVEEDOR POR NECESIDAD (sin repetir hoy)
    // =====================================================================
    private function seleccionarProveedorPorNecesidad(array $proveedoresUsadosHoy): int
    {
        $stockPorProveedor = [];

        foreach ($this->proveedorProductos as $provId => $productos) {
            // Saltar proveedores ya usados hoy
            if (in_array($provId, $proveedoresUsadosHoy)) continue;

            // Todos los proveedores tienen productos reabastecibles
            $tieneReabastecibles = true;

            $stock = 0;
            foreach ($productos as $prodId) {
                $stock += ProductoTallaStock::where('producto_id', $prodId)->sum('stock');
            }
            $stockPorProveedor[$provId] = $stock / count($productos);
        }

        if (empty($stockPorProveedor)) {
            // Todos los proveedores usados hoy, buscar cualquiera disponible
            $todosProveedores = array_keys($this->proveedorProductos);
            $disponibles = array_diff($todosProveedores, $proveedoresUsadosHoy);
            if (!empty($disponibles)) {
                return $disponibles[array_rand($disponibles)];
            }
            // Ultimo recurso: repetir (no deberia llegar aqui con max 5/dia)
            return array_rand($this->proveedorProductos);
        }

        asort($stockPorProveedor);
        $keys = array_keys($stockPorProveedor);

        $rand = mt_rand(1, 100);
        if ($rand <= 55) return $keys[0];
        if ($rand <= 80 && count($keys) > 1) return $keys[1];
        if (count($keys) > 2) return $keys[2];
        return $keys[0];
    }

    // =====================================================================
    // ORDENAR PRODUCTOS POR STOCK ASCENDENTE
    // =====================================================================
    private function ordenarPorStockAscendente(array $productoIds): array
    {
        $stocks = [];
        foreach ($productoIds as $id) {
            $stocks[$id] = ProductoTallaStock::where('producto_id', $id)->sum('stock');
        }
        asort($stocks);
        return array_keys($stocks);
    }

    // =====================================================================
    // VALIDAR COHERENCIA FINAL
    // =====================================================================
    private function validarCoherencia()
    {
        echo str_repeat("=", 70) . "\n";
        echo "  VALIDACIONES DE COHERENCIA\n";
        echo str_repeat("=", 70) . "\n\n";

        $errores = 0;

        $ventasDB = Venta::count();
        $comprasDB = Compra::count();
        $this->validar("Ventas: {$ventasDB}/256", $ventasDB === 256, $errores);
        $this->validar("Compras: {$comprasDB}/50", $comprasDB === 50, $errores);

        $estadoPagado = EstadoTransaccion::where('descripcionET', 'Pagado')->first();
        $estadoPagada = EstadoTransaccion::where('descripcionET', 'Pagada')->first();
        $ventasPagadas = Venta::where('estado_transaccion_id', $estadoPagado->id)->count();
        $comprasPagadas = Compra::where('estado_transaccion_id', $estadoPagada->id)->count();
        $this->validar("Ventas pagadas: {$ventasPagadas}/256", $ventasPagadas === 256, $errores);
        $this->validar("Compras pagadas: {$comprasPagadas}/50", $comprasPagadas === 50, $errores);

        $pagosVenta = Pago::whereNotNull('venta_id')->count();
        $pagosCompra = Pago::whereNotNull('compra_id')->count();
        $this->validar("Pagos de venta: {$pagosVenta}/256", $pagosVenta === 256, $errores);
        $this->validar("Pagos de compra: {$pagosCompra}/50", $pagosCompra === 50, $errores);

        $cajas = Caja::count();
        $this->validar("Cajas creadas: {$cajas}/27", $cajas === 27, $errores);

        $stockNeg = ProductoTallaStock::where('stock', '<', 0)->count();
        $this->validar("Sin stock negativo ({$stockNeg} registros)", $stockNeg === 0, $errores);

        $totalIngresos = round(Caja::sum('ingresoDiario'), 2);
        $totalPagosVenta = round(Pago::whereNotNull('venta_id')->sum('importe'), 2);
        $this->validar(
            "Ingresos caja = Pagos venta (S/ " . number_format($totalIngresos, 2) .
            " vs S/ " . number_format($totalPagosVenta, 2) . ")",
            abs($totalIngresos - $totalPagosVenta) < 0.10,
            $errores
        );

        $totalEgresos = round(Caja::sum('egresoDiario'), 2);
        $totalPagosCompra = round(Pago::whereNotNull('compra_id')->sum('importe'), 2);
        $this->validar(
            "Egresos caja = Pagos compra (S/ " . number_format($totalEgresos, 2) .
            " vs S/ " . number_format($totalPagosCompra, 2) . ")",
            abs($totalEgresos - $totalPagosCompra) < 0.10,
            $errores
        );

        $ventasFueraOct = Venta::whereMonth('created_at', '!=', 10)->count();
        $this->validar("Todas las ventas en octubre", $ventasFueraOct === 0, $errores);

        $comprasFueraOct = Compra::whereMonth('created_at', '!=', 10)->count();
        $this->validar("Todas las compras en octubre", $comprasFueraOct === 0, $errores);

        $fueraHorario = Venta::whereRaw('HOUR(created_at) < 8 OR HOUR(created_at) >= 19')->count();
        $this->validar("Ventas dentro de horario laboral", $fueraHorario === 0, $errores);

        $fueraHorarioC = Compra::whereRaw('HOUR(created_at) < 8 OR HOUR(created_at) >= 19')->count();
        $this->validar("Compras dentro de horario laboral", $fueraHorarioC === 0, $errores);

        // Proveedor no repetido por dia
        $diasConCompra = Compra::selectRaw('DATE(created_at) as dia')->distinct()->pluck('dia');
        $provRepetido = false;
        foreach ($diasConCompra as $dia) {
            $proveedoresDia = Compra::whereDate('created_at', $dia)->pluck('proveedor_id')->toArray();
            if (count($proveedoresDia) !== count(array_unique($proveedoresDia))) {
                $provRepetido = true;
                break;
            }
        }
        $this->validar("Sin proveedor repetido por dia", !$provRepetido, $errores);

        // Stock: ningun producto en 0
        $stockCero = 0;
        foreach (Producto::all() as $prod) {
            if ($prod->stockTotal <= 0) $stockCero++;
        }
        $this->validar("Ningun producto con stock 0 ({$stockCero} encontrados)", $stockCero === 0, $errores);

        // Stock bajo: al menos 3 productos entre 10-15
        $productosStockBajo = 0;
        foreach (Producto::all() as $prod) {
            if ($prod->stockTotal >= 10 && $prod->stockTotal <= 15) $productosStockBajo++;
        }
        $this->validar("Productos con stock 10-15: {$productosStockBajo} (objetivo: >= 3)", $productosStockBajo >= 3, $errores);

        echo "\n  " . ($errores === 0
            ? "TODAS LAS VALIDACIONES PASARON"
            : "{$errores} VALIDACION(ES) FALLARON") . "\n\n";
    }

    private function validar(string $desc, bool $ok, int &$errores)
    {
        echo "    " . ($ok ? "[OK]" : "[!!]") . " {$desc}\n";
        if (!$ok) $errores++;
    }

    // =====================================================================
    // ESTADISTICAS FINALES
    // =====================================================================
    private function mostrarEstadisticas($ventasTotales, $comprasTotales, $resumenDias)
    {
        echo str_repeat("=", 70) . "\n";
        echo "  ESTADISTICAS FINALES - OCTUBRE 2025\n";
        echo str_repeat("=", 70) . "\n\n";

        $totalIngresos = array_sum(array_column($resumenDias, 'ingreso'));
        $totalEgresos = array_sum(array_column($resumenDias, 'egreso'));

        echo "  RESUMEN FINANCIERO:\n";
        echo "    Ventas creadas:    {$ventasTotales}/256\n";
        echo "    Compras creadas:   {$comprasTotales}/50\n";
        echo "    Ingresos totales:  S/ " . number_format($totalIngresos, 2) . "\n";
        echo "    Egresos totales:   S/ " . number_format($totalEgresos, 2) . "\n";
        echo "    Balance neto:      S/ " . number_format($totalIngresos - $totalEgresos, 2) . "\n\n";

        uasort($resumenDias, fn($a, $b) => $b['ingreso'] <=> $a['ingreso']);
        echo "  TOP 5 DIAS CON MAYOR INGRESO:\n";
        $count = 0;
        foreach ($resumenDias as $fecha => $stats) {
            if ($count++ >= 5) break;
            echo "    {$fecha} ({$stats['dia']}): S/ " . number_format($stats['ingreso'], 2);
            echo " ({$stats['ventas']} ventas)\n";
        }

        echo "\n  COMPRAS POR PROVEEDOR:\n";
        $proveedoresDB = Proveedor::all();
        foreach ($proveedoresDB as $prov) {
            $numC = Compra::where('proveedor_id', $prov->id)->count();
            $totalP = Compra::where('proveedor_id', $prov->id)->sum('total');
            echo "    {$prov->nombreEmpresa}: {$numC} compras | S/ " . number_format($totalP, 2) . "\n";
        }

        echo "\n  STOCK FINAL (31/10/2025):\n";
        $productos = Producto::orderBy('id')->get();
        $bajoStock = 0;

        foreach ($productos as $producto) {
            $stock = $producto->stockTotal;

            if ($stock <= 5) {
                $alerta = ' -- CRITICO';
                $bajoStock++;
            } elseif ($stock <= 10) {
                $alerta = ' -- Bajo';
                $bajoStock++;
            } elseif ($stock <= 15) {
                $alerta = ' -- Moderado';
                $bajoStock++;
            } else {
                $alerta = '';
            }

            echo "    #{$producto->id} {$producto->descripcionP}: {$stock} u.{$alerta}\n";
        }

        echo "\n    Productos con stock <= 15: {$bajoStock}/19\n";

        $boletas = Pago::whereNotNull('venta_id')
            ->whereHas('comprobante', fn($q) => $q->where('descripcionCOM', 'Boleta'))
            ->count();
        $facturas = Pago::whereNotNull('venta_id')
            ->whereHas('comprobante', fn($q) => $q->where('descripcionCOM', 'Factura'))
            ->count();

        echo "\n  COMPROBANTES DE VENTA:\n";
        echo "    Boletas:  {$boletas}\n";
        echo "    Facturas: {$facturas}\n\n";

        echo str_repeat("=", 70) . "\n";
        echo "  SEEDER COMPLETADO\n";
        echo str_repeat("=", 70) . "\n\n";
    }

    // =====================================================================
    // UTILIDADES
    // =====================================================================

    private function horaAmPm(Carbon $dt): string
    {
        return $dt->format('g:i:s A');
    }

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
