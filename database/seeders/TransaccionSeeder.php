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
use App\Models\Colaborador;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransaccionSeeder extends Seeder
{
    // =====================================================================
    // OCTUBRE 2025 - MAYO 2026
    // Ventas: Lunes a Sábado, 8am a 8pm.
    // Octubre 2025: Exactamente 256 ventas, 50 compras.
    // Noviembre 2025 - Mayo 2026: Ventas promedio 12/día (10-15), 2-3 prendas c/u.
    // Compras: Reactivas, 2 a 3 veces por semana (miércoles y sábados).
    // Ventas de Colaboradores (Laura, Sofia, Jacky).
    // Compras de Gerente (Elyana).
    // =====================================================================

    // Relación de Proveedor ID => Productos (1 proveedor = 1 categoria)
    private $proveedorProductos = [
        1 => [1, 2, 3, 4, 5],       // Moda Eclipse => Polos & Camisetas
        2 => [6, 7, 8, 9, 10],      // Estilo Urbano => Jeans & Pantalones
        3 => [11, 12, 13],           // Hilos de Plata => Shorts & Bermudas
        4 => [14, 15, 16],           // Ropa Estelar => Abrigos & Chaquetas
        5 => [17, 18, 19],           // Textiles de Oro => Ropa Deportiva
    ];

    private $productoProveedor = [
        1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1,
        6 => 2, 7 => 2, 8 => 2, 9 => 2, 10 => 2,
        11 => 3, 12 => 3, 13 => 3,
        14 => 4, 15 => 4, 16 => 4,
        17 => 5, 18 => 5, 19 => 5,
    ];

    // Popularidad de venta (mayor = se vende mas)
    private $productoPesos = [
        1 => 7, 2 => 7, 3 => 6, 4 => 5, 5 => 5,      // Polos
        6 => 5, 7 => 4, 8 => 4, 9 => 3, 10 => 3,     // Jeans
        11 => 3, 12 => 3, 13 => 2,                    // Shorts
        14 => 3, 15 => 6, 16 => 4,                    // Abrigos
        17 => 4, 18 => 5, 19 => 7,                    // Deportiva
    ];

    // Tallas por producto
    private $tallasProducto = [
        1 => [1,2,3,4], 2 => [1,2,3,4], 3 => [1,2,3,4], 4 => [1,2,3,4], 5 => [1,2,3,4],
        6 => [5,6,7,8], 7 => [5,6,7,8], 8 => [5,6,7,8], 9 => [5,6,7,8], 10 => [5,6,7,8],
        11 => [5,6,7,8], 12 => [5,6,7,8], 13 => [5,6,7,8],
        14 => [1,2,3,4], 15 => [1,2,3,4], 16 => [1,2,3,4],
        17 => [1,2,3,4], 18 => [1,2,3,4], 19 => [1,2,3,4],
    ];

    // Objetivo de productos con stock mínimo en el Dashboard (5 a 7 productos)
    // Definimos exactamente 6 productos con stock bajo al final del periodo
    private $stockBajo = [3, 9, 13, 14, 16, 18];

    public function run()
    {
        // Usar semilla aleatoria fija para consistencia en la generación
        mt_srand(20251001);

        // 1. LIMPIAR BASE DE DATOS
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        VentaDetalle::truncate();
        CompraDetalle::truncate();
        Pago::truncate();
        Venta::truncate();
        Compra::truncate();
        Caja::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. ACTUALIZAR CLIENTES (Todos permanecen con su DNI de 8 dígitos)
        $clientes = Cliente::all();
        if ($clientes->isEmpty()) {
            echo "ERROR: Debe sembrar los clientes antes de ejecutar este seeder.\n";
            return;
        }

        // Obtener modelos base necesarios
        $estadoPendiente = EstadoTransaccion::where('descripcionET', 'Pendiente')->first();
        $estadoPagado = EstadoTransaccion::where('descripcionET', 'Pagado')->first();
        $estadoPagada = EstadoTransaccion::where('descripcionET', 'Pagada')->first();
        $comprobanteBoleta = Comprobante::where('descripcionCOM', 'Boleta')->first();
        $comprobanteFactura = Comprobante::where('descripcionCOM', 'Factura')->first();

        if (!$estadoPendiente || !$estadoPagado || !$estadoPagada || !$comprobanteBoleta || !$comprobanteFactura) {
            echo "ERROR: Faltan estados de transacción o tipos de comprobantes base.\n";
            return;
        }

        $vendedores = Colaborador::whereIn('nombreColab', ['Laura', 'Sofia', 'Jacky'])->get();
        if ($vendedores->isEmpty()) {
            $vendedores = Colaborador::where('cargo_id', 2)->get();
        }
        $gerente = Colaborador::where('nombreColab', 'Elyana')->first() 
            ?? Colaborador::where('cargo_id', 1)->first();

        // Generar lista de días laborables (Octubre 2025 a Mayo 2026, sin domingos)
        $diasOperacion = [];
        $startDate = Carbon::create(2025, 10, 1);
        $endDate = Carbon::create(2026, 5, 31);
        
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            if ($current->dayOfWeek !== Carbon::SUNDAY) {
                $isOctober = ($current->year === 2025 && $current->month === 10);
                $diasOperacion[] = [
                    'fecha' => $current->format('Y-m-d'),
                    'isOctober' => $isOctober,
                ];
            }
            $current->addDay();
        }
        
        $totalDias = count($diasOperacion);

        echo "\n" . str_repeat("=", 70) . "\n";
        echo "  ELI BOUTIQUE - SEEDER UNIFICADO DE TRANSACCIONES OCT 2025 A MAY 2026\n";
        echo "  Total de días laborables detectados: {$totalDias}\n";
        echo str_repeat("=", 70) . "\n\n";

        // Distribución de Ventas para Octubre 2025 (27 días laborables, total 256 ventas)
        // 27 * 8 = 216. Faltan 40. Sumamos 1 venta a los primeros 40 índices módulo 27.
        // Dando: 13 días con 10 ventas, 14 días con 9 ventas. Suma = 130 + 126 = 256.
        $salesOctDistribution = array_fill(0, 27, 8);
        for ($i = 0; $i < 40; $i++) {
            $salesOctDistribution[$i % 27]++;
        }

        // Distribución de Compras para Octubre 2025 (total 50 compras)
        // Se distribuyen en los 9 miércoles y sábados de octubre:
        // [5, 5, 5, 5, 6, 6, 6, 6, 6] = 50 compras.
        $purchasesOctCounts = [5, 5, 5, 5, 6, 6, 6, 6, 6];
        $octoberPurchaseIdx = 0;
        $compraOctTotalIndex = 0;

        $octoberDaysCount = 0;
        $vendedorIndex = 0;
        $ventasTotales = 0;
        $comprasTotales = 0;

        // Procesar día a día
        foreach ($diasOperacion as $diaInfo) {
            $fecha = $diaInfo['fecha'];
            $isOctober = $diaInfo['isOctober'];
            $carbonFecha = Carbon::parse($fecha);
            $diaSemana = ucfirst($carbonFecha->locale('es')->dayName);
            $esMiercolesOSabado = in_array($carbonFecha->dayOfWeek, [Carbon::WEDNESDAY, Carbon::SATURDAY]);

            // Determinar números de ventas y compras para el día
            if ($isOctober) {
                $numVentas = $salesOctDistribution[$octoberDaysCount];
                $numCompras = 0;
                if ($esMiercolesOSabado && $octoberPurchaseIdx < count($purchasesOctCounts)) {
                    $numCompras = $purchasesOctCounts[$octoberPurchaseIdx];
                    $octoberPurchaseIdx++;
                }
                $octoberDaysCount++;
            } else {
                // Noviembre - Mayo: Ventas aleatorias 10-15 (Promedio ~12.5), Compras reactivas en miércoles/sábados
                $numVentas = mt_rand(10, 15);
                $numCompras = $esMiercolesOSabado ? 1 : 0; // 1 indica que es día de compras reactivas
            }

            // 1. ABRIR CAJA DIARIA
            $caja = Caja::create([
                'fecha' => $fecha,
                'clientesHoy' => 0,
                'productosVendidos' => 0,
                'ingresoDiario' => 0.00,
                'egresoDiario' => 0.00,
                'created_at' => $carbonFecha->copy()->setTime(8, mt_rand(0, 5), mt_rand(0, 59)),
                'updated_at' => $carbonFecha->copy()->setTime(8, mt_rand(0, 5), mt_rand(0, 59)),
            ]);

            echo "  Día {$fecha} ({$diaSemana}) | Caja #{$caja->codigoCaja}\n";

            // 2. COMPRAS
            if ($numCompras > 0) {
                if ($isOctober) {
                    // Para Octubre: Generar exactamente $numCompras registros de Compra (compras totales)
                    $currentMin = mt_rand(10, 15);
                    for ($c = 0; $c < $numCompras; $c++) {
                        $proveedorId = (($compraOctTotalIndex) % 5) + 1;
                        $compraOctTotalIndex++;
                        
                        $proveedor = Proveedor::find($proveedorId);
                        $productosDelProveedor = $this->proveedorProductos[$proveedorId];
                        
                        $diasAntes = mt_rand(4, 6);
                        $fechaEnvio = $carbonFecha->copy()->subDays($diasAntes)->format('Y-m-d');
                        $fechaCotizacion = $carbonFecha->copy()->subDays($diasAntes - mt_rand(1, 2))->format('Y-m-d');
                        $fechaAprobacion = $carbonFecha->copy()->subDays(mt_rand(1, 2))->format('Y-m-d');
                        $horaCompra = $carbonFecha->copy()->setTime(8, $currentMin, mt_rand(0, 59));
                        $currentMin += mt_rand(4, 9);

                        $compra = Compra::create([
                            'proveedor_id' => $proveedorId,
                            'comprobante_id' => $comprobanteFactura->id,
                            'estado_transaccion_id' => $estadoPagada->id,
                            'colaborador_id' => $gerente ? $gerente->id : null,
                            'fecha_envio' => $fechaEnvio,
                            'fecha_cotizacion' => $fechaCotizacion,
                            'fecha_aprobacion' => $fechaAprobacion,
                            'fecha_entrega_estimada' => $fecha,
                            'condiciones_pago' => 'Pago contra entrega',
                            'subtotal' => 0,
                            'descuento' => 0,
                            'igv' => 0,
                            'total' => 0,
                            'created_at' => $horaCompra,
                            'updated_at' => $horaCompra,
                        ]);

                        $subtotalCompra = 0;
                        foreach ($productosDelProveedor as $productoId) {
                            $producto = Producto::find($productoId);
                            $tallas = $this->tallasProducto[$productoId];
                            $tallaId = $tallas[array_rand($tallas)]; // Escoger una talla

                            $cantidad = mt_rand(5, 8);
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
                                'created_at' => $horaCompra,
                                'updated_at' => $horaCompra,
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
                            'created_at' => $horaCompra,
                            'updated_at' => $horaCompra,
                        ]);

                        $caja->increment('egresoDiario', $totalCompra);
                        $comprasTotales++;
                    }
                } else {
                    // Para Noviembre - Mayo: Compras Reactivas basadas en stock bajo (<= 15)
                    $tallasAReabastecer = [];
                    $tallasStocks = ProductoTallaStock::all();
                    
                    foreach ($tallasStocks as $tallaStock) {
                        $productoId = $tallaStock->producto_id;
                        $tallaId = $tallaStock->producto_talla_id;
                        $stockActual = $tallaStock->stock;

                        if ($stockActual <= 15) {
                            if (in_array($productoId, $this->stockBajo)) {
                                if ($stockActual <= 1) {
                                    $tallasAReabastecer[$productoId][] = [
                                        'talla_id' => $tallaId,
                                        'cantidad' => 3,
                                    ];
                                }
                            } else {
                                $cantidadCompra = 18 - $stockActual;
                                if ($cantidadCompra > 0) {
                                    $tallasAReabastecer[$productoId][] = [
                                        'talla_id' => $tallaId,
                                        'cantidad' => $cantidadCompra,
                                    ];
                                }
                            }
                        }
                    }

                    $comprasPorProveedor = [];
                    foreach ($tallasAReabastecer as $productoId => $detallesTalla) {
                        $proveedorId = $this->productoProveedor[$productoId];
                        foreach ($detallesTalla as $det) {
                            $comprasPorProveedor[$proveedorId][] = [
                                'producto_id' => $productoId,
                                'talla_id' => $det['talla_id'],
                                'cantidad' => $det['cantidad']
                            ];
                        }
                    }

                    $currentMin = mt_rand(10, 15);
                    foreach ($comprasPorProveedor as $proveedorId => $items) {
                        $diasAntes = mt_rand(4, 6);
                        $fechaEnvio = $carbonFecha->copy()->subDays($diasAntes)->format('Y-m-d');
                        $fechaCotizacion = $carbonFecha->copy()->subDays($diasAntes - mt_rand(1, 2))->format('Y-m-d');
                        $fechaAprobacion = $carbonFecha->copy()->subDays(mt_rand(1, 2))->format('Y-m-d');
                        $horaCompra = $carbonFecha->copy()->setTime(8, $currentMin, mt_rand(0, 59));
                        $currentMin += mt_rand(4, 9);

                        $compra = Compra::create([
                            'proveedor_id' => $proveedorId,
                            'comprobante_id' => $comprobanteFactura->id,
                            'estado_transaccion_id' => $estadoPagada->id,
                            'colaborador_id' => $gerente ? $gerente->id : null,
                            'fecha_envio' => $fechaEnvio,
                            'fecha_cotizacion' => $fechaCotizacion,
                            'fecha_aprobacion' => $fechaAprobacion,
                            'fecha_entrega_estimada' => $fecha,
                            'condiciones_pago' => 'Pago contra entrega',
                            'subtotal' => 0,
                            'descuento' => 0,
                            'igv' => 0,
                            'total' => 0,
                            'created_at' => $horaCompra,
                            'updated_at' => $horaCompra,
                        ]);

                        $subtotalCompra = 0;
                        foreach ($items as $item) {
                            $producto = Producto::find($item['producto_id']);
                            $precioCompra = round($producto->precioP * 0.55, 2);
                            $subtotalLinea = round($item['cantidad'] * $precioCompra, 2);

                            CompraDetalle::create([
                                'compra_id' => $compra->id,
                                'producto_id' => $item['producto_id'],
                                'producto_talla_id' => $item['talla_id'],
                                'cantidad' => $item['cantidad'],
                                'precio_cotizado' => $precioCompra,
                                'precio_final' => $precioCompra,
                                'subtotal_linea' => $subtotalLinea,
                                'created_at' => $horaCompra,
                                'updated_at' => $horaCompra,
                            ]);

                            ProductoTallaStock::where('producto_id', $item['producto_id'])
                                ->where('producto_talla_id', $item['talla_id'])
                                ->increment('stock', $item['cantidad']);

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
                            'created_at' => $horaCompra,
                            'updated_at' => $horaCompra,
                        ]);

                        $caja->increment('egresoDiario', $totalCompra);
                        $comprasTotales++;
                    }
                }
            }

            // 3. HORAS SECUENCIALES PARA LAS VENTAS DEL DÍA
            $horas = $this->generarHorasSecuenciales($carbonFecha, $numVentas);

            // 4. GENERAR VENTAS DIARIAS
            $clientesDia = Cliente::all(); // Recargar clientes

            for ($v = 0; $v < $numVentas; $v++) {
                $horaVenta = $horas[$v];
                $cliente = $clientesDia[mt_rand(0, $clientesDia->count() - 1)];

                // Al azar, ~25% de las ventas generadas en el seeder serán Facturas
                $esFactura = (mt_rand(1, 100) <= 25);
                $comprobante = $esFactura ? $comprobanteFactura : $comprobanteBoleta;

                $rucFactura = null;
                $razonSocialFactura = null;

                if ($esFactura) {
                    $rucFactura = '20' . mt_rand(10000000, 99999999) . mt_rand(0, 9);
                    $empresasNombres = [
                        'Inversiones Textiles del Norte',
                        'Comercializadora Pacanga',
                        'Boutique Elegance',
                        'Distribuidora Trujillo',
                        'Servicios Generales Chepén',
                        'Textil San Andrés',
                        'Creaciones e Importaciones Chiclayo',
                        'Corporación de la Moda del Perú'
                    ];
                    $societarios = ['S.A.C.', 'E.I.R.L.', 'S.R.L.'];
                    $razonSocialFactura = $empresasNombres[array_rand($empresasNombres)] . ' ' . $societarios[array_rand($societarios)];
                }

                // Cada venta tiene exactamente 2 o 3 prendas
                $totalPrendasVenta = mt_rand(2, 3);
                $detallesData = [];
                $productosUsados = [];

                if ($totalPrendasVenta === 2) {
                    $opcion = mt_rand(1, 100) <= 60 ? 'distintos' : 'unico';
                    if ($opcion === 'distintos') {
                        $detallesData = $this->seleccionarProductosConStock($productosUsados, [1, 1]);
                    } else {
                        $detallesData = $this->seleccionarProductosConStock($productosUsados, [2]);
                    }
                } else {
                    $rand = mt_rand(1, 100);
                    if ($rand <= 50) {
                        $detallesData = $this->seleccionarProductosConStock($productosUsados, [2, 1]);
                    } elseif ($rand <= 90) {
                        $detallesData = $this->seleccionarProductosConStock($productosUsados, [1, 1, 1]);
                    } else {
                        $detallesData = $this->seleccionarProductosConStock($productosUsados, [3]);
                    }
                }

                if (empty($detallesData)) {
                    $detallesData = $this->seleccionarProductosConStock($productosUsados, [1, 1]);
                    if (empty($detallesData)) {
                        continue;
                    }
                }

                $montoTotalVenta = 0;
                foreach ($detallesData as $det) {
                    $montoTotalVenta += $det['subtotal'];
                }

                $subTotalVenta = round($montoTotalVenta / 1.18, 2);
                $igvVenta = round($montoTotalVenta - $subTotalVenta, 2);

                $vendedor = $vendedores->isNotEmpty()
                    ? $vendedores->values()->get($vendedorIndex % $vendedores->count())
                    : null;
                $vendedorIndex++;

                $venta = Venta::create([
                    'caja_id' => $caja->id,
                    'cliente_id' => $cliente->id,
                    'estado_transaccion_id' => $estadoPendiente->id,
                    'colaborador_id' => $vendedor ? $vendedor->id : null,
                    'subTotal' => $subTotalVenta,
                    'IGV' => $igvVenta,
                    'montoTotal' => round($montoTotalVenta, 2),
                    'ruc_factura' => $rucFactura,
                    'razon_social_factura' => $razonSocialFactura,
                    'created_at' => $horaVenta,
                    'updated_at' => $horaVenta,
                ]);

                foreach ($detallesData as $det) {
                    VentaDetalle::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $det['producto_id'],
                        'producto_talla_id' => $det['talla_id'],
                        'cantidad' => $det['cantidad'],
                        'precio_unitario' => $det['precio_unitario'],
                        'base_imponible' => $det['base_imponible'],
                        'igv' => $det['igv'],
                        'subtotal' => $det['subtotal'],
                        'created_at' => $horaVenta,
                        'updated_at' => $horaVenta,
                    ]);

                    ProductoTallaStock::where('producto_id', $det['producto_id'])
                        ->where('producto_talla_id', $det['talla_id'])
                        ->decrement('stock', $det['cantidad']);
                }

                Pago::create([
                    'venta_id' => $venta->id,
                    'importe' => round($montoTotalVenta, 2),
                    'vuelto' => 0,
                    'comprobante_id' => $comprobante->id,
                    'created_at' => $horaVenta,
                    'updated_at' => $horaVenta,
                ]);

                $venta->estado_transaccion_id = $estadoPagado->id;
                $venta->timestamps = false;
                $venta->save();

                $ventasTotales++;
            }

            $caja->refresh();
            $balance = $caja->ingresoDiario - $caja->egresoDiario;
            echo "    Ingreso: S/ " . number_format($caja->ingresoDiario, 2) . " | Egreso: S/ " . number_format($caja->egresoDiario, 2) . " | Balance: S/ " . number_format($balance, 2) . "\n";
        }

        // 5. CALIBRACIÓN/AJUSTE DE STOCK FINAL AL 31 DE MAYO DE 2026
        // Garantizar que exactamente los 6 productos de stockBajo terminen con stock total <= 15.
        // El resto debe terminar en un nivel de stock saludable (entre 20 y 45).
        $this->ajustarStockFinal();

        // 6. VALIDACIONES Y ESTADÍSTICAS FINALES
        $this->validarCoherencia();
    }

    private function generarHorasSecuenciales(Carbon $fecha, int $total): array
    {
        $inicioSeg = 8 * 3600 + 6 * 60;    // 08:06:00
        $finSeg    = 18 * 3600 + 47 * 60;  // 18:47:00
        $rangoTotal = $finSeg - $inicioSeg;

        $horas = [];
        if ($total <= 0) return $horas;
        
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

    private function seleccionarProductosConStock(array &$productosUsados, array $cantidades): array
    {
        $detalles = [];
        foreach ($cantidades as $cantidad) {
            $intentos = 0;
            $productoEncontrado = false;
            
            while ($intentos < 20) {
                $productoId = $this->randomPonderado($this->productoPesos);
                
                if (in_array($productoId, $productosUsados)) {
                    $intentos++;
                    continue;
                }
                
                $tallaInfo = $this->obtenerTallaConStock($productoId, $cantidad);
                if ($tallaInfo) {
                    $producto = Producto::find($productoId);
                    $precioUnitario = $producto->precioP;
                    $baseImponible = round($precioUnitario / 1.18, 2);
                    $igv = round($precioUnitario - $baseImponible, 2);
                    
                    $detalles[] = [
                        'producto_id' => $productoId,
                        'talla_id' => $tallaInfo['talla_id'],
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'base_imponible' => $baseImponible,
                        'igv' => $igv,
                        'subtotal' => $cantidad * $precioUnitario,
                    ];
                    
                    $productosUsados[] = $productoId;
                    $productoEncontrado = true;
                    break;
                }
                $intentos++;
            }
            
            if (!$productoEncontrado) {
                return []; 
            }
        }
        return $detalles;
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

    private function ajustarStockFinal()
    {
        echo "\n" . str_repeat("-", 70) . "\n";
        echo "  CALIBRACIÓN FINAL DE STOCK AL 31 DE MAYO DE 2026:\n";
        echo str_repeat("-", 70) . "\n";
        
        $productos = Producto::orderBy('id')->get();

        foreach ($productos as $producto) {
            $stockActual = $producto->stockTotal;
            $esStockBajo = in_array($producto->id, $this->stockBajo);
            $minTarget = $esStockBajo ? 8 : 20;
            $maxTarget = $esStockBajo ? 15 : 45;

            if ($stockActual >= $minTarget && $stockActual <= $maxTarget) continue;

            $objetivo = $esStockBajo ? mt_rand(9, 14) : mt_rand(25, 38);
            $delta = $objetivo - $stockActual;

            if ($delta == 0) continue;

            if ($delta > 0) {
                $detalle = CompraDetalle::where('producto_id', $producto->id)
                    ->orderBy('id', 'desc')->first();

                if ($detalle) {
                    $detalle->cantidad += $delta;
                    $detalle->subtotal_linea = round($detalle->cantidad * $detalle->precio_final, 2);
                    $detalle->save();

                    ProductoTallaStock::where('producto_id', $producto->id)
                        ->where('producto_talla_id', $detalle->producto_talla_id)
                        ->increment('stock', $delta);

                    $this->recalcularCompra($detalle->compra_id);
                    echo "    #{$producto->id} {$producto->descripcionP}: {$stockActual} -> {$objetivo} (+{$delta} via compra ID {$detalle->compra_id})\n";
                }
            } else {
                $reducir = abs($delta);
                $detalles = CompraDetalle::where('producto_id', $producto->id)
                    ->orderBy('id', 'desc')->get();

                foreach ($detalles as $detalle) {
                    if ($reducir <= 0) break;
                    $maxReduccion = $detalle->cantidad - 1;

                    $stockTalla = ProductoTallaStock::where('producto_id', $producto->id)
                        ->where('producto_talla_id', $detalle->producto_talla_id)->value('stock') ?? 0;
                    $maxReduccion = min($maxReduccion, $stockTalla - 1); // Mantener stock > 0
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
                echo "    #{$producto->id} {$producto->descripcionP}: {$stockActual} -> {$stockFinal} (-" . (abs($delta) - $reducir) . " via compras)\n";
            }
        }
        echo "\n";
    }

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

        $fechaCompra = Carbon::parse($compra->created_at)->format('Y-m-d');
        $caja = Caja::where('fecha', $fechaCompra)->first();
        if ($caja) {
            $caja->egresoDiario += ($total - $oldTotal);
            $caja->save();
        }
    }

    private function validarCoherencia()
    {
        echo str_repeat("=", 70) . "\n";
        echo "  VALIDACIONES DE COHERENCIA - OCTUBRE 2025 A MAYO 2026\n";
        echo str_repeat("=", 70) . "\n\n";

        $errores = 0;

        $ventasDB = Venta::count();
        $comprasDB = Compra::count();
        
        // Comprobar Octubre de forma exclusiva
        $ventasOct = Venta::whereYear('created_at', 2025)->whereMonth('created_at', 10)->count();
        $comprasOct = Compra::whereYear('created_at', 2025)->whereMonth('created_at', 10)->count();

        $this->validar("Ventas en Octubre: {$ventasOct}/256", $ventasOct === 256, $errores);
        $this->validar("Compras en Octubre: {$comprasOct}/50", $comprasOct === 50, $errores);

        // Sin existencias negativas
        $stockNeg = ProductoTallaStock::where('stock', '<', 0)->count();
        $this->validar("Sin stock negativo en tienda (0 registros)", $stockNeg === 0, $errores);

        // Sin stock en 0
        $stockCero = 0;
        foreach (Producto::all() as $prod) {
            if ($prod->stockTotal <= 0) $stockCero++;
        }
        $this->validar("Ningún producto con stock 0 ({$stockCero} encontrados)", $stockCero === 0, $errores);

        // Horario laboral
        $fueraHorario = Venta::whereRaw('HOUR(created_at) < 8 OR HOUR(created_at) >= 20')->count();
        $this->validar("Ventas realizadas dentro de horario laboral (08:00 AM - 08:00 PM)", $fueraHorario === 0, $errores);

        $fueraHorarioC = Compra::whereRaw('HOUR(created_at) < 8 OR HOUR(created_at) >= 20')->count();
        $this->validar("Compras realizadas dentro de horario laboral (08:00 AM - 08:00 PM)", $fueraHorarioC === 0, $errores);

        // Domingos no se trabaja
        $enDomingo = Venta::whereRaw('DAYOFWEEK(created_at) = 1')->count();
        $this->validar("Transacciones en domingo: {$enDomingo} (deben ser 0)", $enDomingo === 0, $errores);

        // Margen y balance positivo global
        $totalIngresos = round(Caja::sum('ingresoDiario'), 2);
        $totalEgresos = round(Caja::sum('egresoDiario'), 2);
        $balanceNeto = $totalIngresos - $totalEgresos;
        $this->validar("Balance general positivo (Ingresos: S/ " . number_format($totalIngresos, 2) . " > Egresos: S/ " . number_format($totalEgresos, 2) . ")", $balanceNeto > 0, $errores);

        // Dashboard Alerta de Stock Mínimo (5 a 7 productos con stock <= 15)
        $productosBajoStock = 0;
        foreach (Producto::all() as $prod) {
            if ($prod->stockTotal <= 15) {
                $productosBajoStock++;
            }
        }
        $this->validar("Dashboard Alerta Stock Mínimo: {$productosBajoStock} productos con stock <= 15 (objetivo: entre 5 y 7)", ($productosBajoStock >= 5 && $productosBajoStock <= 7), $errores);

        // Asociaciones de colaboradores
        $comprasSinColab = Compra::whereNull('colaborador_id')->count();
        $ventasSinColab = Venta::whereNull('colaborador_id')->count();
        $this->validar("Compras con gerente asignado ({$comprasSinColab} sin asignar)", $comprasSinColab === 0, $errores);
        $this->validar("Ventas con vendedores asignados ({$ventasSinColab} sin asignar)", $ventasSinColab === 0, $errores);

        echo "\n  " . ($errores === 0
            ? "TODAS LAS VALIDACIONES PASARON EXITOSAMENTE"
            : "{$errores} VALIDACION(ES) FALLARON") . "\n\n";

        echo "  RESUMEN ESTADÍSTICO DE SIEMBRA:\n";
        echo "    Total Ventas Sembradas: " . number_format($ventasDB) . "\n";
        echo "    Total Compras Sembradas: " . number_format($comprasDB) . "\n";
        echo "    Balance de Caja Neto: S/ " . number_format($balanceNeto, 2) . "\n\n";
    }

    private function validar(string $desc, bool $ok, int &$errores)
    {
        echo "    " . ($ok ? "[OK]" : "[!!]") . " {$desc}\n";
        if (!$ok) $errores++;
    }
}
