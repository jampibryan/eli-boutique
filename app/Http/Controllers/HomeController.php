<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\EstadoTransaccion;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    public function __construct()
    {
        $this->middleware('auth');
    }

 
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Verificar el estado de la caja hoy (en tiempo real)
        $cajaHoy = Caja::whereDate('fecha', now()->toDateString())->first();
        
        // Determinar estados
        $cajaSinIniciar = !$cajaHoy;
        $cajaAbierta = $cajaHoy && empty($cajaHoy->hora_cierre);
        $cajaCerrada = $cajaHoy && !empty($cajaHoy->hora_cierre);

        // Métricas financieras y productos (cacheadas por 45s para navegación ultra rápida sin saturar WAN)
        $dashboardStats = \Illuminate\Support\Facades\Cache::remember('dashboard_stats_' . now()->toDateString(), 45, function () use ($cajaHoy) {
            // Contar clientes únicos con compras hoy (SSOT: cajaHoy->clientesHoy)
            $clientesCount = $cajaHoy ? (int) $cajaHoy->clientesHoy : (int) Venta::whereDate('created_at', now()->toDateString())
                ->whereHas('estadoTransaccion', fn($q) => $q->where('descripcionET', 'Pagado'))
                ->distinct('cliente_id')
                ->count('cliente_id');

            // Total productos vendidos hoy (SSOT: cajaHoy->productosVendidos)
            $productosCount = $cajaHoy ? (int) $cajaHoy->productosVendidos : (int) VentaDetalle::whereHas('venta', function ($query) {
                    $query->whereDate('created_at', now()->toDateString())
                        ->whereHas('estadoTransaccion', fn($q) => $q->where('descripcionET', 'Pagado'));
                })
                ->sum('cantidad');

            // Ingreso total de ventas pagadas hoy (SSOT: cajas.ingresoDiario)
            $ingresoDiario = $cajaHoy ? (float) $cajaHoy->ingresoDiario : (float) Venta::whereDate('created_at', now()->toDateString())
                ->whereHas('estadoTransaccion', fn($q) => $q->where('descripcionET', 'Pagado'))
                ->sum('montoTotal');

            // Gastos reales de caja del día por compras pagadas (SSOT: cajas.egresoDiario)
            $gastosDiario = $cajaHoy ? (float) $cajaHoy->egresoDiario : 0.0;

            // Balance neto del día en caja
            $balanceDiario = $ingresoDiario - $gastosDiario;

            // Estados de recepción para compras
            $estadosGastos = EstadoTransaccion::whereIn('descripcionET', ['Recibida', 'Pagada'])->pluck('id');

            // Compras recibidas/pagadas hoy
            $comprasRecibidasCount = Compra::whereDate('updated_at', now()->toDateString())
                ->whereIn('estado_transaccion_id', $estadosGastos)
                ->count();

            // Top 5 productos más vendidos
            $productosMasVendidos = VentaDetalle::with('producto')
                ->selectRaw('producto_id, SUM(cantidad) as total_vendido')
                ->whereHas('venta.estadoTransaccion', fn($q) => $q->where('descripcionET', 'Pagado'))
                ->groupBy('producto_id')
                ->orderBy('total_vendido', 'desc')
                ->take(5)
                ->get();

            // Cargar productos y stock una sola vez en lugar de dos veces
            $productosConStock = Producto::withoutTrashed()
                ->with('tallaStocks')
                ->get()
                ->map(function ($producto) {
                    $producto->stockP = (int) $producto->tallaStocks->sum('stock');
                    return $producto;
                });

            // Productos con stock total mínimo (15 o menos)
            $productosStockMinimo = $productosConStock
                ->filter(fn($producto) => $producto->stockP <= 15)
                ->values();

            // Stock actual de productos
            $productosStockActual = $productosConStock
                ->map(fn($producto) => [
                    'descripcionP' => $producto->descripcionP,
                    'stockP' => $producto->stockP,
                ])
                ->values();

            return compact(
                'clientesCount',
                'productosCount',
                'ingresoDiario',
                'comprasRecibidasCount',
                'gastosDiario',
                'balanceDiario',
                'productosStockMinimo',
                'productosMasVendidos',
                'productosStockActual'
            );
        });

        return view('home', array_merge(
            compact('cajaAbierta', 'cajaCerrada', 'cajaSinIniciar'),
            $dashboardStats
        ));
    }
}
