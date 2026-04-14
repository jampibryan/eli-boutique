<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Caja;
use App\Models\CategoriaProducto;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\EstadoTransaccion;
use App\Models\Producto;
use App\Models\ProductoTalla;
use App\Models\Proveedor;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    // =====================================================================
    //  CLIENTES
    // =====================================================================

    public function clientes(): JsonResponse
    {
        $clientes = Cliente::with('tipoGenero')->get();
        return response()->json(['success' => true, 'data' => $clientes]);
    }

    public function clienteShow($id): JsonResponse
    {
        $cliente = Cliente::with('tipoGenero')->find($id);

        if (!$cliente) {
            return response()->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
        }

        return response()->json(['success' => true, 'data' => $cliente]);
    }

    // =====================================================================
    //  PRODUCTOS
    // =====================================================================

    public function productos(): JsonResponse
    {
        $productos = Producto::with(['categoriaProducto', 'productoGenero', 'tallaStocks.talla'])
            ->get()
            ->map(function ($producto) {
                $producto->stock_total = $producto->stockTotal;
                return $producto;
            });

        return response()->json(['success' => true, 'data' => $productos]);
    }

    public function productoShow($id): JsonResponse
    {
        $producto = Producto::with(['categoriaProducto', 'productoGenero', 'tallaStocks.talla'])
            ->find($id);

        if (!$producto) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
        }

        $producto->stock_total = $producto->stockTotal;

        return response()->json(['success' => true, 'data' => $producto]);
    }

    public function categorias(): JsonResponse
    {
        $categorias = CategoriaProducto::all();
        return response()->json(['success' => true, 'data' => $categorias]);
    }

    public function tallas(): JsonResponse
    {
        $tallas = ProductoTalla::all();
        return response()->json(['success' => true, 'data' => $tallas]);
    }

    // =====================================================================
    //  VENTAS
    // =====================================================================

    public function ventas(): JsonResponse
    {
        $ventas = Venta::with(['cliente', 'estadoTransaccion', 'detalles.producto', 'detalles.talla', 'pago.comprobante'])
            ->whereHas('estadoTransaccion')
            ->whereHas('pago.comprobante')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $ventas]);
    }

    public function ventaShow($id): JsonResponse
    {
        $venta = Venta::with(['cliente', 'estadoTransaccion', 'detalles.producto', 'detalles.talla', 'pago.comprobante'])
            ->find($id);

        if (!$venta) {
            return response()->json(['success' => false, 'message' => 'Venta no encontrada'], 404);
        }

        return response()->json(['success' => true, 'data' => $venta]);
    }

    /**
     * Datos aplanados de ventas para ML / predicción (Streamlit).
     */
    public function ventasDatosML(): JsonResponse
    {
        $ventas = Venta::with(['detalles.producto'])->get();

        $resultados = collect();

        foreach ($ventas as $venta) {
            if ($venta->detalles && $venta->detalles->count() > 0) {
                foreach ($venta->detalles as $detalle) {
                    if ($detalle->producto) {
                        $resultados->push([
                            'venta_id'         => $venta->id,
                            'producto_id'      => $detalle->producto_id,
                            'producto_nombre'   => $detalle->producto->descripcionP,
                            'cantidad_vendida'  => $detalle->cantidad,
                            'año'              => $venta->created_at->year,
                            'mes'              => $venta->created_at->month,
                            'dia'              => $venta->created_at->day,
                        ]);
                    }
                }
            }
        }

        return response()->json($resultados->all());
    }

    // =====================================================================
    //  COMPRAS
    // =====================================================================

    public function compras(): JsonResponse
    {
        $compras = Compra::with(['proveedor', 'detalles.producto', 'pago', 'estadoTransaccion', 'comprobante'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $compras]);
    }

    public function compraShow($id): JsonResponse
    {
        $compra = Compra::with(['proveedor', 'detalles.producto', 'pago', 'estadoTransaccion', 'comprobante'])
            ->find($id);

        if (!$compra) {
            return response()->json(['success' => false, 'message' => 'Compra no encontrada'], 404);
        }

        return response()->json(['success' => true, 'data' => $compra]);
    }

    // =====================================================================
    //  PROVEEDORES
    // =====================================================================

    public function proveedores(): JsonResponse
    {
        $proveedores = Proveedor::all();
        return response()->json(['success' => true, 'data' => $proveedores]);
    }

    public function proveedorShow($id): JsonResponse
    {
        $proveedor = Proveedor::find($id);

        if (!$proveedor) {
            return response()->json(['success' => false, 'message' => 'Proveedor no encontrado'], 404);
        }

        return response()->json(['success' => true, 'data' => $proveedor]);
    }

    // =====================================================================
    //  CAJAS
    // =====================================================================

    public function cajas(): JsonResponse
    {
        $cajas = Caja::orderBy('fecha', 'desc')->get();
        return response()->json(['success' => true, 'data' => $cajas]);
    }

    public function cajaShow($id): JsonResponse
    {
        $caja = Caja::with('ventas.cliente')->find($id);

        if (!$caja) {
            return response()->json(['success' => false, 'message' => 'Caja no encontrada'], 404);
        }

        $caja->balance_diario = $caja->balanceDiario;

        return response()->json(['success' => true, 'data' => $caja]);
    }

    // =====================================================================
    //  DASHBOARD / RESUMEN
    // =====================================================================

    public function dashboard(): JsonResponse
    {
        $hoy = now()->toDateString();

        $cajaHoy = Caja::where('fecha', $hoy)->first();

        $estadoPagado = EstadoTransaccion::where('descripcionET', 'Pagado')->first();

        $totalClientes = Cliente::count();
        $totalProductos = Producto::count();
        $totalProveedores = Proveedor::count();

        $ventasHoy = $cajaHoy ? Venta::where('caja_id', $cajaHoy->id)
            ->where('estado_transaccion_id', optional($estadoPagado)->id)
            ->count() : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'fecha'             => $hoy,
                'caja'              => $cajaHoy,
                'ventas_hoy'        => $ventasHoy,
                'total_clientes'    => $totalClientes,
                'total_productos'   => $totalProductos,
                'total_proveedores' => $totalProveedores,
            ],
        ]);
    }

    // =====================================================================
    //  ESTADOS DE TRANSACCIÓN (catálogo)
    // =====================================================================

    public function estadosTransaccion(): JsonResponse
    {
        $estados = EstadoTransaccion::all();
        return response()->json(['success' => true, 'data' => $estados]);
    }
}
