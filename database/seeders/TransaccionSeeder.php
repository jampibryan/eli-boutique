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

class TransaccionSeeder extends Seeder
{
    // =====================================================================
    // NOVIEMBRE 2025 - ENERO 2026
    // ~10 ventas/dia promedio | Compras: miercoles y sabados
    // Pedidos controlados, margen de beneficio positivo
    // Misma logica realista que TransaccionSeederOct
    // =====================================================================

    // [fecha, numVentas, numCompras]
    // Noviembre 2025: Lu-Sa (25 dias operativos)
    // Diciembre 2025: Lu-Sa (27 dias operativos) — temporada alta
    // Enero 2026: Lu-Sa (27 dias operativos)
    private $diasOperacion = [
        // ==================== NOVIEMBRE 2025 ====================
        // Semana 1 (Sa 1)
        ['2025-11-01', 11, 4],    // Sabado
        // Semana 2 (Lu 3 - Sa 8)
        ['2025-11-03', 9, 0],     // Lunes
        ['2025-11-04', 10, 0],    // Martes
        ['2025-11-05', 11, 4],    // Miercoles
        ['2025-11-06', 9, 0],     // Jueves
        ['2025-11-07', 10, 0],    // Viernes
        ['2025-11-08', 12, 4],    // Sabado
        // Semana 3 (Lu 10 - Sa 15)
        ['2025-11-10', 9, 0],     // Lunes
        ['2025-11-11', 10, 0],    // Martes
        ['2025-11-12', 10, 3],    // Miercoles
        ['2025-11-13', 8, 0],     // Jueves
        ['2025-11-14', 9, 0],     // Viernes
        ['2025-11-15', 11, 4],    // Sabado
        // Semana 4 (Lu 17 - Sa 22)
        ['2025-11-17', 10, 0],    // Lunes
        ['2025-11-18', 9, 0],     // Martes
        ['2025-11-19', 11, 3],    // Miercoles
        ['2025-11-20', 10, 0],    // Jueves
        ['2025-11-21', 9, 0],     // Viernes
        ['2025-11-22', 12, 4],    // Sabado
        // Semana 5 (Lu 24 - Sa 29)
        ['2025-11-24', 10, 0],    // Lunes
        ['2025-11-25', 9, 0],     // Martes
        ['2025-11-26', 10, 3],    // Miercoles
        ['2025-11-27', 11, 0],    // Jueves
        ['2025-11-28', 9, 0],     // Viernes
        ['2025-11-29', 11, 4],    // Sabado

        // ==================== DICIEMBRE 2025 ====================
        // Semana 1 (Lu 1 - Sa 6)
        ['2025-12-01', 11, 0],    // Lunes
        ['2025-12-02', 10, 0],    // Martes
        ['2025-12-03', 12, 4],    // Miercoles
        ['2025-12-04', 10, 0],    // Jueves
        ['2025-12-05', 11, 0],    // Viernes
        ['2025-12-06', 13, 5],    // Sabado — temporada alta
        // Semana 2 (Lu 8 - Sa 13)
        ['2025-12-08', 10, 0],    // Lunes
        ['2025-12-09', 11, 0],    // Martes
        ['2025-12-10', 12, 4],    // Miercoles
        ['2025-12-11', 10, 0],    // Jueves
        ['2025-12-12', 11, 0],    // Viernes
        ['2025-12-13', 14, 5],    // Sabado — pico navideño
        // Semana 3 (Lu 15 - Sa 20)
        ['2025-12-15', 12, 0],    // Lunes
        ['2025-12-16', 11, 0],    // Martes
        ['2025-12-17', 13, 4],    // Miercoles
        ['2025-12-18', 12, 0],    // Jueves
        ['2025-12-19', 13, 0],    // Viernes
        ['2025-12-20', 15, 5],    // Sabado — semana pre-navidad
        // Semana 4 (Lu 22 - Mi 24) — pre-Navidad
        ['2025-12-22', 14, 0],    // Lunes
        ['2025-12-23', 13, 0],    // Martes
        ['2025-12-24', 11, 4],    // Miercoles — Nochebuena (medio dia)
        // No hay jueves 25 (feriado Navidad), ni viernes/sabado
        ['2025-12-26', 10, 0],    // Viernes
        ['2025-12-27', 12, 5],    // Sabado
        // Semana 5 (Lu 29 - Mi 31)
        ['2025-12-29', 10, 0],    // Lunes
        ['2025-12-30', 9, 0],     // Martes
        ['2025-12-31', 8, 3],     // Miercoles — fin de año (medio dia)

        // ==================== ENERO 2026 ====================
        // No hay jueves 1 (feriado Año Nuevo)
        // Semana 1 (Vi 2 - Sa 3)
        ['2026-01-02', 8, 0],     // Viernes — regreso
        ['2026-01-03', 10, 4],    // Sabado
        // Semana 2 (Lu 5 - Sa 10)
        ['2026-01-05', 9, 0],     // Lunes
        ['2026-01-06', 10, 0],    // Martes
        ['2026-01-07', 10, 3],    // Miercoles
        ['2026-01-08', 9, 0],     // Jueves
        ['2026-01-09', 10, 0],    // Viernes
        ['2026-01-10', 11, 4],    // Sabado
        // Semana 3 (Lu 12 - Sa 17)
        ['2026-01-12', 9, 0],     // Lunes
        ['2026-01-13', 10, 0],    // Martes
        ['2026-01-14', 10, 3],    // Miercoles
        ['2026-01-15', 9, 0],     // Jueves
        ['2026-01-16', 8, 0],     // Viernes
        ['2026-01-17', 11, 4],    // Sabado
        // Semana 4 (Lu 19 - Sa 24)
        ['2026-01-19', 9, 0],     // Lunes
        ['2026-01-20', 10, 0],    // Martes
        ['2026-01-21', 10, 3],    // Miercoles
        ['2026-01-22', 9, 0],     // Jueves
        ['2026-01-23', 8, 0],     // Viernes
        ['2026-01-24', 11, 4],    // Sabado
        // Semana 5 (Lu 26 - Sa 31)
        ['2026-01-26', 9, 0],     // Lunes
        ['2026-01-27', 8, 0],     // Martes
        ['2026-01-28', 10, 3],    // Miercoles
        ['2026-01-29', 9, 0],     // Jueves
        ['2026-01-30', 10, 0],    // Viernes
        ['2026-01-31', 11, 4],    // Sabado
    ];

    // Proveedor ID => Productos (1 proveedor = 1 categoria)
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

    // Objetivo de stock POR TALLA para compras reactivas
    // Valores altos = compras mas agresivas para mantener stock saludable
    private $objetivoPorProducto = [
        1 => 10, 2 => 10, 3 => 9, 4 => 9, 5 => 9,
        6 => 9, 7 => 9, 8 => 9, 9 => 8, 10 => 8,
        11 => 8, 12 => 8, 13 => 8,
        14 => 8, 15 => 9, 16 => 8,
        17 => 9, 18 => 9, 19 => 10,
    ];

    // 5-8 productos terminaran con stock bajo (8-15 unidades totales)
    // El resto terminara con stock saludable (20-45)
    private $stockBajo = [3, 9, 13, 14, 16, 18];

    // =====================================================================
    // METODO PRINCIPAL
    // =====================================================================
    public function run()
    {
        mt_srand(2026);

        // Contar totales del array
        $totalVentasEsperadas = array_sum(array_column($this->diasOperacion, 1));
        $totalComprasEsperadas = array_sum(array_column($this->diasOperacion, 2));
        $totalDias = count($this->diasOperacion);

        echo "\n" . str_repeat("=", 70) . "\n";
        echo "  ELI BOUTIQUE - SEEDER NOVIEMBRE 2025 A ENERO 2026\n";
        echo "  ~{$totalVentasEsperadas} Ventas + ~{$totalComprasEsperadas} Compras | {$totalDias} dias operativos\n";
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

        // Verificar que ya existen datos de octubre
        $ventasOct = Venta::whereYear('created_at', 2025)->whereMonth('created_at', 10)->count();
        if ($ventasOct === 0) {
            echo "ERROR: No se encontraron ventas de octubre. Ejecute TransaccionSeederOct primero.\n";
            return;
        }
        echo "  Datos previos: {$ventasOct} ventas de octubre detectadas\n\n";

        $ventasTotales = 0;
        $comprasTotales = 0;
        $resumenDias = [];
        $mesActual = '';

        // ==================== PROCESAR DIA A DIA ====================
        foreach ($this->diasOperacion as [$fecha, $numVentas, $numCompras]) {
            $carbonFecha = Carbon::parse($fecha);
            $diaSemana = ucfirst($carbonFecha->locale('es')->dayName);
            $totalTransacciones = $numVentas + $numCompras;

            // Separador por mes
            $mes = $carbonFecha->format('F Y');
            if ($mes !== $mesActual) {
                $mesActual = $mes;
                $mesNombre = $this->nombreMes($carbonFecha->month) . ' ' . $carbonFecha->year;
                echo "\n  " . str_repeat("=", 50) . "\n";
                echo "  {$mesNombre}\n";
                echo "  " . str_repeat("=", 50) . "\n";
            }

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

            echo "  " . str_repeat("-", 60) . "\n";
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
                echo " | Balance: S/ " . number_format($caja->ingresoDiario - $caja->egresoDiario, 2);
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

        // AJUSTE FINAL: garantizar stock saludable
        // 5-8 productos con stock 8-15 (stock minimo)
        // El resto con stock 20-45 (saludable)
        // Ninguno en 0
        $this->ajustarStockFinal();

        $this->validarCoherencia($ventasTotales, $comprasTotales);
        $this->mostrarEstadisticas($ventasTotales, $comprasTotales, $resumenDias);
    }

    // =====================================================================
    // GENERAR HORAS SECUENCIALES
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
    // Pedidos controlados: reponer solo lo necesario, margen de beneficio alto
    // =====================================================================
    private function crearCompra(Carbon $fechaHora, $estadoPagada, $comprobanteFactura, $caja, array &$proveedoresUsadosHoy)
    {
        $proveedorId = $this->seleccionarProveedorPorNecesidad($proveedoresUsadosHoy);
        $proveedoresUsadosHoy[] = $proveedorId;

        $proveedor = Proveedor::find($proveedorId);
        $productosDelProveedor = $this->proveedorProductos[$proveedorId];

        $productosReabastecibles = array_values($productosDelProveedor);

        // 2-4 items por compra (mas agresivo para mantener stock)
        $maxItems = min(4, count($productosReabastecibles));
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

            // Elegir la talla con menor stock
            $mejorTalla = null;
            $menorStock = PHP_INT_MAX;
            foreach ($tallas as $tid) {
                $st = ProductoTallaStock::where('producto_id', $productoId)
                    ->where('producto_talla_id', $tid)->value('stock') ?? 0;
                if ($st < $menorStock) {
                    $menorStock = $st;
                    $mejorTalla = $tid;
                }
            }

            $tallaId = $mejorTalla ?? $tallas[0];

            // Calcular cantidad segun stock actual vs objetivo
            $stockActual = ProductoTallaStock::where('producto_id', $productoId)
                ->where('producto_talla_id', $tallaId)->value('stock') ?? 0;

            $objetivoTalla = $this->objetivoPorProducto[$productoId] ?? 8;
            $deficit = max(0, $objetivoTalla - $stockActual);

            // Compras agresivas: reponer deficit + 1-3 extras para buffer
            $cantidad = max(2, min($deficit + mt_rand(1, 3), 10));

            // Precio de compra = 55% del precio de venta (margen bruto ~45%)
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

                $tallaInfo = $this->obtenerTallaConStock($candidato, 3);
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
    // SELECCIONAR PROVEEDOR POR NECESIDAD
    // =====================================================================
    private function seleccionarProveedorPorNecesidad(array $proveedoresUsadosHoy): int
    {
        $stockPorProveedor = [];

        foreach ($this->proveedorProductos as $provId => $productos) {
            if (in_array($provId, $proveedoresUsadosHoy)) continue;

            $stock = 0;
            foreach ($productos as $prodId) {
                $stock += ProductoTallaStock::where('producto_id', $prodId)->sum('stock');
            }
            $stockPorProveedor[$provId] = $stock / count($productos);
        }

        if (empty($stockPorProveedor)) {
            $todosProveedores = array_keys($this->proveedorProductos);
            $disponibles = array_diff($todosProveedores, $proveedoresUsadosHoy);
            if (!empty($disponibles)) {
                return $disponibles[array_rand($disponibles)];
            }
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
    // AJUSTE FINAL DE STOCK
    // Modifica cantidades de CompraDetalle existentes (solo Nov-Ene)
    // para que el stock aterrice en los rangos objetivo.
    // stockBajo (6 productos): 8-15 unidades totales
    // Normal (13 productos): 20-45 unidades totales
    // Ninguno en 0
    // =====================================================================
    private function ajustarStockFinal()
    {
        echo "  AJUSTE FINAL DE STOCK:\n";
        $productos = Producto::orderBy('id')->get();

        foreach ($productos as $producto) {
            $stockActual = $producto->stockTotal;
            $esStockBajo = in_array($producto->id, $this->stockBajo);
            $minTarget = $esStockBajo ? 8 : 20;
            $maxTarget = $esStockBajo ? 15 : 45;

            if ($stockActual >= $minTarget && $stockActual <= $maxTarget) continue;

            // Calcular stock objetivo
            $objetivo = $esStockBajo ? mt_rand(9, 14) : mt_rand(25, 38);
            $delta = $objetivo - $stockActual;

            if ($delta == 0) continue;

            if ($delta > 0) {
                // NECESITA MAS STOCK: buscar CompraDetalle del periodo Nov-Ene y aumentar
                $detalle = CompraDetalle::where('producto_id', $producto->id)
                    ->whereHas('compra', function ($q) {
                        $q->where('created_at', '>=', '2025-11-01');
                    })
                    ->orderBy('id', 'desc')->first();

                if ($detalle) {
                    $detalle->cantidad += $delta;
                    $detalle->subtotal_linea = round($detalle->cantidad * $detalle->precio_final, 2);
                    $detalle->save();

                    ProductoTallaStock::where('producto_id', $producto->id)
                        ->where('producto_talla_id', $detalle->producto_talla_id)
                        ->increment('stock', $delta);

                    $this->recalcularCompra($detalle->compra_id);
                    echo "    #{$producto->id} {$producto->descripcionP}: {$stockActual} -> {$objetivo} (+{$delta} via compra)\n";
                }
            } else {
                // STOCK DEMASIADO ALTO: reducir CompraDetalle del periodo Nov-Ene
                $reducir = abs($delta);
                $detalles = CompraDetalle::where('producto_id', $producto->id)
                    ->whereHas('compra', function ($q) {
                        $q->where('created_at', '>=', '2025-11-01');
                    })
                    ->orderBy('id', 'desc')->get();

                foreach ($detalles as $detalle) {
                    if ($reducir <= 0) break;
                    $maxReduccion = $detalle->cantidad - 1;

                    $stockTalla = ProductoTallaStock::where('producto_id', $producto->id)
                        ->where('producto_talla_id', $detalle->producto_talla_id)->value('stock') ?? 0;
                    $maxReduccion = min($maxReduccion, $stockTalla - 1); // mantener al menos 1 en stock
                    $reduccionReal = min($reducir, max(0, $maxReduccion));

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
                $stockFinal = $objetivo + $reducir;
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

        $pago = Pago::where('compra_id', $compraId)->first();
        if ($pago) {
            $pago->importe = $total;
            $pago->save();
        }

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
    // VALIDACIONES
    // =====================================================================
    private function validarCoherencia($ventasTotales, $comprasTotales)
    {
        echo str_repeat("=", 70) . "\n";
        echo "  VALIDACIONES - NOV 2025 A ENE 2026\n";
        echo str_repeat("=", 70) . "\n\n";

        $errores = 0;

        // Verificar que no hay stock negativo
        $stockNeg = ProductoTallaStock::where('stock', '<', 0)->count();
        $this->validar("Sin stock negativo ({$stockNeg} registros)", $stockNeg === 0, $errores);

        // Verificar ventas dentro de horario
        $fueraHorario = Venta::where(function($q) {
            $q->where('created_at', '>=', '2025-11-01')
              ->where('created_at', '<', '2026-02-01');
        })->whereRaw('HOUR(created_at) < 8 OR HOUR(created_at) >= 19')->count();
        $this->validar("Ventas Nov-Ene dentro de horario laboral", $fueraHorario === 0, $errores);

        // Proveedor no repetido por dia
        $diasConCompra = Compra::where('created_at', '>=', '2025-11-01')
            ->selectRaw('DATE(created_at) as dia')->distinct()->pluck('dia');
        $provRepetido = false;
        foreach ($diasConCompra as $dia) {
            $proveedoresDia = Compra::whereDate('created_at', $dia)->pluck('proveedor_id')->toArray();
            if (count($proveedoresDia) !== count(array_unique($proveedoresDia))) {
                $provRepetido = true;
                break;
            }
        }
        $this->validar("Sin proveedor repetido por dia", !$provRepetido, $errores);

        // Ningun producto con stock 0
        $stockCero = 0;
        foreach (Producto::all() as $prod) {
            if ($prod->stockTotal <= 0) $stockCero++;
        }
        $this->validar("Ningun producto con stock 0 ({$stockCero} encontrados)", $stockCero === 0, $errores);

        // Margen beneficio: ingresos > egresos en periodos Nov-Ene
        $ingresosNovEne = Caja::where('fecha', '>=', '2025-11-01')
            ->where('fecha', '<', '2026-02-01')->sum('ingresoDiario');
        $egresosNovEne = Caja::where('fecha', '>=', '2025-11-01')
            ->where('fecha', '<', '2026-02-01')->sum('egresoDiario');
        $this->validar(
            "Margen positivo Nov-Ene: Ingresos S/ " . number_format($ingresosNovEne, 2) .
            " > Egresos S/ " . number_format($egresosNovEne, 2),
            $ingresosNovEne > $egresosNovEne,
            $errores
        );

        $this->validar("Ventas creadas: {$ventasTotales}", $ventasTotales > 0, $errores);
        $this->validar("Compras creadas: {$comprasTotales}", $comprasTotales > 0, $errores);

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
    // ESTADISTICAS
    // =====================================================================
    private function mostrarEstadisticas($ventasTotales, $comprasTotales, $resumenDias)
    {
        echo str_repeat("=", 70) . "\n";
        echo "  ESTADISTICAS - NOVIEMBRE 2025 A ENERO 2026\n";
        echo str_repeat("=", 70) . "\n\n";

        $totalIngresos = array_sum(array_column($resumenDias, 'ingreso'));
        $totalEgresos = array_sum(array_column($resumenDias, 'egreso'));

        echo "  RESUMEN FINANCIERO:\n";
        echo "    Ventas creadas:    {$ventasTotales}\n";
        echo "    Compras creadas:   {$comprasTotales}\n";
        echo "    Ingresos totales:  S/ " . number_format($totalIngresos, 2) . "\n";
        echo "    Egresos totales:   S/ " . number_format($totalEgresos, 2) . "\n";
        echo "    Balance neto:      S/ " . number_format($totalIngresos - $totalEgresos, 2) . "\n";
        echo "    Margen:            " . round(($totalIngresos - $totalEgresos) / $totalIngresos * 100, 1) . "%\n\n";

        // Resumen por mes
        $meses = ['11' => 'Noviembre', '12' => 'Diciembre', '01' => 'Enero'];
        foreach ($meses as $numMes => $nombreMes) {
            $diasMes = array_filter($resumenDias, function ($k) use ($numMes) {
                return substr($k, 5, 2) === $numMes;
            }, ARRAY_FILTER_USE_KEY);

            if (empty($diasMes)) continue;

            $ventasMes = array_sum(array_column($diasMes, 'ventas'));
            $comprasMes = array_sum(array_column($diasMes, 'compras'));
            $ingresoMes = array_sum(array_column($diasMes, 'ingreso'));
            $egresoMes = array_sum(array_column($diasMes, 'egreso'));

            echo "  {$nombreMes}:\n";
            echo "    Dias: " . count($diasMes) . " | Ventas: {$ventasMes} | Compras: {$comprasMes}\n";
            echo "    Ingreso: S/ " . number_format($ingresoMes, 2);
            echo " | Egreso: S/ " . number_format($egresoMes, 2);
            echo " | Balance: S/ " . number_format($ingresoMes - $egresoMes, 2) . "\n\n";
        }

        // Top 5 dias mejor ingreso
        uasort($resumenDias, fn($a, $b) => $b['ingreso'] <=> $a['ingreso']);
        echo "  TOP 5 DIAS CON MAYOR INGRESO:\n";
        $count = 0;
        foreach ($resumenDias as $fecha => $stats) {
            if ($count++ >= 5) break;
            echo "    {$fecha} ({$stats['dia']}): S/ " . number_format($stats['ingreso'], 2);
            echo " ({$stats['ventas']} ventas)\n";
        }

        echo "\n  STOCK FINAL:\n";
        $productos = Producto::orderBy('id')->get();
        foreach ($productos as $producto) {
            $stock = $producto->stockTotal;
            $alerta = '';
            if ($stock <= 5) $alerta = ' -- CRITICO';
            elseif ($stock <= 10) $alerta = ' -- Bajo';
            elseif ($stock <= 15) $alerta = ' -- Moderado';

            echo "    #{$producto->id} {$producto->descripcionP}: {$stock} u.{$alerta}\n";
        }

        echo "\n" . str_repeat("=", 70) . "\n";
        echo "  SEEDER NOV-ENE COMPLETADO\n";
        echo str_repeat("=", 70) . "\n\n";
    }

    // =====================================================================
    // UTILIDADES
    // =====================================================================
    private function horaAmPm(Carbon $dt): string
    {
        return $dt->format('g:i:s A');
    }

    private function obtenerTallaConStock($productoId, $minStock = 1)
    {
        $tallas = $this->tallasProducto[$productoId] ?? [];
        $tallasShuffled = $tallas;
        shuffle($tallasShuffled);

        foreach ($tallasShuffled as $tallaId) {
            $stock = ProductoTallaStock::where('producto_id', $productoId)
                ->where('producto_talla_id', $tallaId)
                ->value('stock');

            if ($stock && $stock >= $minStock) {
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

    private function nombreMes(int $mes): string
    {
        $nombres = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];
        return $nombres[$mes] ?? '';
    }
}
