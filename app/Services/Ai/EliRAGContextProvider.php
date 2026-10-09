<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class EliRAGContextProvider
{
    /**
     * Construye un resumen en vivo del estado del ERP según el módulo y pantalla.
     */
    public function buildContext(?string $modulo = null, ?string $route = null, ?string $pregunta = null): string
    {
        $sb = [];
        $nowLima = now()->setTimezone('America/Lima')->format('d/m/Y H:i:s');
        $sb[] = "=== SISTEMA ERP ELI BOUTIQUE (FECHA ACTUAL PERÚ: {$nowLima}) ===";

        $this->appendCajaContext($sb);
        $this->appendStockCriticoContext($sb);
        $this->appendVentasRecientesContext($sb);
        $this->appendComprasRecientesContext($sb);

        return implode("\n\n", $sb);
    }

    private function appendCajaContext(array &$sb): void
    {
        try {
            $caja = DB::table('cajas')->orderByDesc('id')->first();
            if ($caja) {
                $estado = empty($caja->hora_cierre) ? 'ABIERTA (En Operación)' : 'CERRADA a las ' . $caja->hora_cierre;
                $ingreso = number_format((float) ($caja->ingresoDiario ?? 0), 2);
                $egreso = number_format((float) ($caja->egresoDiario ?? 0), 2);
                $saldo = number_format((float) (($caja->ingresoDiario ?? 0) - ($caja->egresoDiario ?? 0)), 2);

                $sb[] = "--- [ESTADO DE CAJA DIARIA ({$caja->codigoCaja})] ---" .
                    "\n• Fecha: {$caja->fecha} | Estado: {$estado}" .
                    "\n• Ingresos por Ventas: S/ {$ingreso} | Egresos: S/ {$egreso} | Saldo Neto: S/ {$saldo}" .
                    "\n• Clientes atendidos: {$caja->clientesHoy} | Prendas vendidas hoy: {$caja->productosVendidos}";
            } else {
                $sb[] = "--- [ESTADO DE CAJA] ---\nNo hay cajas registradas actualmente.";
            }
        } catch (Throwable $e) {
            Log::warning("Error al construir contexto de Caja: " . $e->getMessage());
        }
    }

    private function appendStockCriticoContext(array &$sb): void
    {
        try {
            $criticos = DB::table('producto_talla_stock as pts')
                ->join('productos as p', 'p.id', '=', 'pts.producto_id')
                ->join('producto_tallas as pt', 'pt.id', '=', 'pts.producto_talla_id')
                ->whereNull('p.deleted_at')
                ->where('pts.stock', '<=', 3)
                ->select('p.codigoP', 'p.descripcionP', 'pt.descripcion as talla', 'pts.stock')
                ->orderBy('pts.stock', 'asc')
                ->limit(5)
                ->get();

            if ($criticos->isNotEmpty()) {
                $lineas = ["--- [ALERTAS DE STOCK CRÍTICO (≤ 3 unidades por talla)] ---"];
                foreach ($criticos as $c) {
                    $lineas[] = "• {$c->codigoP} - {$c->descripcionP} | Talla {$c->talla}: {$c->stock} unidad(es)";
                }
                $sb[] = implode("\n", $lineas);
            }
        } catch (Throwable $e) {
            Log::warning("Error al construir contexto de Stock Crítico: " . $e->getMessage());
        }
    }

    private function appendVentasRecientesContext(array &$sb): void
    {
        try {
            $ventas = DB::table('ventas as v')
                ->leftJoin('clientes as c', 'c.id', '=', 'v.cliente_id')
                ->select(
                    'v.codigoVenta',
                    'v.montoTotal',
                    'v.created_at',
                    'c.nombreCliente',
                    'c.apellidoCliente'
                )
                ->orderByDesc('v.id')
                ->limit(5)
                ->get();

            if ($ventas->isNotEmpty()) {
                $lineas = ["--- [ÚLTIMAS VENTAS REGISTRADAS] ---"];
                foreach ($ventas as $v) {
                    $total = number_format((float) $v->montoTotal, 2);
                    $cliente = trim(($v->nombreCliente ?? '') . ' ' . ($v->apellidoCliente ?? ''));
                    if (empty($cliente)) {
                        $cliente = 'Cliente Varios';
                    }
                    $fecha = date('d/m/Y H:i', strtotime($v->created_at));
                    $lineas[] = "• Venta {$v->codigoVenta} ({$fecha}): S/ {$total} | Cliente: {$cliente}";
                }
                $sb[] = implode("\n", $lineas);
            }
        } catch (Throwable $e) {
            Log::warning("Error al construir contexto de Ventas Recientes: " . $e->getMessage());
        }
    }

    private function appendComprasRecientesContext(array &$sb): void
    {
        try {
            $compras = DB::table('compras as c')
                ->leftJoin('proveedores as p', 'p.id', '=', 'c.proveedor_id')
                ->leftJoin('estado_transacciones as et', 'et.id', '=', 'c.estado_transaccion_id')
                ->select(
                    'c.codigoCompra',
                    'c.total',
                    'c.created_at',
                    'p.nombreEmpresa',
                    'et.descripcionET as estado'
                )
                ->orderByDesc('c.id')
                ->limit(3)
                ->get();

            if ($compras->isNotEmpty()) {
                $lineas = ["--- [ÚLTIMAS COMPRAS A PROVEEDORES] ---"];
                foreach ($compras as $c) {
                    $total = number_format((float) $c->total, 2);
                    $prov = $c->nombreEmpresa ?? 'S/P';
                    $estado = $c->estado ?? 'Registrada';
                    $lineas[] = "• Compra {$c->codigoCompra}: S/ {$total} | Prov: {$prov} | Estado: {$estado}";
                }
                $sb[] = implode("\n", $lineas);
            }
        } catch (Throwable $e) {
            Log::warning("Error al construir contexto de Compras Recientes: " . $e->getMessage());
        }
    }
}
