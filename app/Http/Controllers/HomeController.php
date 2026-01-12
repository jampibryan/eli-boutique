<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
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
        // Verificar si la caja está abierta hoy
        $cajaAbierta = Caja::whereDate('fecha', now()->toDateString())->exists();

        // Reseteo de la sesión si es un nuevo día
        if (!$cajaAbierta) {
            session()->forget('cajaCerrada'); // Borra la variable de sesión
        }

        // Contar la cantidad de clientes únicos que realizaron compras hoy
        $clientesCount = Venta::whereDate('created_at', now()->toDateString())
            ->whereHas('estadoTransaccion', function ($query) {
                $query->where('descripcionET', 'Pagado');
            })
            ->distinct('cliente_id')
            ->count('cliente_id');

        // Contar la cantidad total de productos vendidos hoy
        $productosCount = VentaDetalle::whereHas('venta', function ($query) {
                $query->whereDate('created_at', now()->toDateString())
                    ->whereHas('estadoTransaccion', function ($subQuery) {
                        $subQuery->where('descripcionET', 'Pagado');
                    });
            })
            ->sum('cantidad'); // Suma la cantidad de productos vendidos hoy

        // Obtener el ingreso total de las ventas realizadas hoy con estado pagado
        $ingresoDiario = Venta::whereDate('created_at', now()->toDateString())
            ->whereHas('estadoTransaccion', function ($query) {
                $query->where('descripcionET', 'Pagado');
            })                    
            ->sum('montoTotal');

        // Obtener productos con stock total mínimo (15 o menos sumando todas las tallas)
        $productosStockMinimo = Producto::with('tallaStocks')
            ->get()
            ->map(function($producto) {
                $producto->stockP = $producto->tallaStocks->sum('stock');
                return $producto;
            })
            ->filter(function($producto) {
                return $producto->stockP <= 15;
            })
            ->values();

        // Obtener los 5 productos más vendidos
        $productosMasVendidos = VentaDetalle::with('producto')
            ->selectRaw('producto_id, SUM(cantidad) as total_vendido')
            ->whereHas('venta.estadoTransaccion', function ($query) {
                $query->where('descripcionET', 'Pagado');
            })
            ->groupBy('producto_id')
            ->orderBy('total_vendido', 'desc')
            ->take(5)
            ->get();

        // Obtener la lista de productos con su stock actual (suma de todas las tallas)
        $productosStockActual = Producto::with('tallaStocks')
            ->get()
            ->map(function($producto) {
                return [
                    'descripcionP' => $producto->descripcionP,
                    'stockP' => $producto->tallaStocks->sum('stock')
                ];
            });

        return view('home', compact('cajaAbierta', 'clientesCount', 'productosCount', 'ingresoDiario', 'productosStockMinimo', 'productosMasVendidos', 'productosStockActual'));
    }
}
