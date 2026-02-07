<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Colaborador;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Comprobante;
use App\Models\EstadoTransaccion;
use App\Models\Producto;
use App\Models\ProductoTalla;
use App\Models\ProductoTallaStock;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\OrdenCompraEnviada;

class CompraController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware para verificar permisos
        $this->middleware('permission:gestionar compras', ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);
    }

    public function apiCompras()
    {
        // Cargar las compras con sus detalles y pagos
        $compras = Compra::with(['proveedor', 'detalles', 'pago'])->get();
        return view('Compra.index', compact('compras'));
    }




    public function pdfCompras()
    {
        $compras = Compra::whereNotNull('id')->get();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Compra.reporte', compact('compras')));

        // return $pdf->download(); //Descarga automática
        return $pdf->stream('Reporte de Compras.pdf'); //Abre una pestaña
    }

    public function pdfOrdenCompra(Compra $compra)
    {
        // Cargar todas las relaciones necesarias
        $compra->load(['proveedor', 'detalles.producto', 'detalles.talla', 'comprobante', 'estadoTransaccion']); 

        // Obtener el colaborador
        $colaborador = Colaborador::find(1);

        // Generar el PDF
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Compra.orden', compact('compra', 'colaborador')));

        return $pdf->stream('Orden de compra - ' . $compra->codigoCompra . '.pdf');
    }


    public function index()
    {
        // Cargar las compras con sus detalles y pagos
        $compras = Compra::with(['proveedor', 'detalles.producto', 'detalles.talla', 'pago', 'estadoTransaccion', 'comprobante'])
            ->orderBy('id', 'desc')
            ->get();
        return view('Compra.index', compact('compras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $proveedores = Proveedor::withoutTrashed()->get();
        $productos = Producto::withoutTrashed()->with(['tallaStocks.talla'])->get();
        $tallas = ProductoTalla::all();
        return view('Compra.create', compact('proveedores', 'productos', 'tallas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.talla_id' => 'required|exists:producto_tallas,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        // ==================== VERIFICAR CAJA ABIERTA ====================
        $cajaHoy = Caja::whereDate('fecha', today())->first();

        if (!$cajaHoy) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No se puede crear órdenes de compra. Debes abrir la caja primero.');
        }

        if ($cajaHoy->hora_cierre) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No se puede crear órdenes de compra. La caja del día ya está cerrada.');
        }
        // ================================================================

        $compra = Compra::create([
            'proveedor_id' => $request->proveedor_id,
        ]);

        foreach ($request->productos as $productoData) {
            CompraDetalle::create([
                'compra_id' => $compra->id,
                'producto_id' => $productoData['id'],
                'producto_talla_id' => $productoData['talla_id'],
                'cantidad' => $productoData['cantidad'],
            ]);
        }

        return redirect()->route('compras.index')->with('success', 'Orden de compra creada. Estado: Borrador');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Compra $compra)
    {
        $compra->load(['detalles.producto.tallaStocks.talla', 'detalles.talla']);
        $proveedores = Proveedor::withoutTrashed()->get();
        $productos = Producto::withoutTrashed()->with(['tallaStocks.talla'])->get();
        $tallas = ProductoTalla::all();
        return view('Compra.edit', compact('compra', 'proveedores', 'productos', 'tallas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Compra $compra)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.talla_id' => 'required|exists:producto_tallas,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $compra->proveedor_id = $request->proveedor_id;
        $compra->save();

        CompraDetalle::where('compra_id', $compra->id)->delete();

        foreach ($request->productos as $productoData) {
            $productoDetalle = new CompraDetalle();
            $productoDetalle->compra_id = $compra->id;
            $productoDetalle->producto_id = $productoData['id'];
            $productoDetalle->producto_talla_id = $productoData['talla_id'];
            $productoDetalle->cantidad = $productoData['cantidad'];
            $productoDetalle->save();
        }

        return redirect()->route('compras.index')->with('success', 'Compra actualizada con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Compra $compra)
    {
        $compra->delete();
        return redirect()->route('compras.index')->with('success', 'Compra eliminada con éxito.');
    }

    public function recibirPedido($compraId)
    {
        $compra = Compra::with('detalles')->findOrFail($compraId);
        
        // Verificar que la orden esté en estado Aprobada
        if ($compra->estadoTransaccion->descripcionET !== 'Aprobada') {
            return redirect()->route('compras.index')->with('error', 'Solo se pueden recibir órdenes en estado Aprobada.');
        }
        
        // Buscar el estado correcto (puede ser "Recibida" o "Recibido")
        $estadoRecibido = EstadoTransaccion::whereIn('descripcionET', ['Recibida', 'Recibido'])->first();

        if ($estadoRecibido) {
            $compra->estado_transaccion_id = $estadoRecibido->id;
            $compra->save();
        }

        // Actualizar stock de cada producto
        foreach ($compra->detalles as $detalle) {
            $tallaStock = ProductoTallaStock::where('producto_id', $detalle->producto_id)
                ->where('producto_talla_id', $detalle->producto_talla_id)
                ->first();
            
            if ($tallaStock) {
                $tallaStock->stock += $detalle->cantidad;
                $tallaStock->save();
            } else {
                ProductoTallaStock::create([
                    'producto_id' => $detalle->producto_id,
                    'producto_talla_id' => $detalle->producto_talla_id,
                    'stock' => $detalle->cantidad
                ]);
            }
        }

        return redirect()->route('compras.index')->with('success', 'Mercadería recibida y stock actualizado. Ahora puede proceder a pagar al proveedor.');
    }

    public function anularCompra($compraId)
    {
        $compra = Compra::findOrFail($compraId);
        if ($compra->estadoTransaccion->descripcionET !== 'Anulado') {
            $compra->anular();
            return redirect()->route('compras.index')->with('success', 'Compra anulada correctamente.');
        } else {
            return redirect()->route('compras.index')->with('error', 'La compra ya está anulada.');
        }
    }

    // Enviar orden al proveedor
    public function enviarCompra($compraId)
    {
        $compra = Compra::with(['detalles.producto', 'detalles.talla', 'proveedor', 'comprobante', 'estadoTransaccion'])->findOrFail($compraId);
        
        // Verificar que esté en estado Borrador
        if ($compra->estadoTransaccion->descripcionET !== 'Borrador') {
            return redirect()->route('compras.index')->with('error', 'Solo se pueden enviar órdenes en estado Borrador.');
        }

        // Cambiar estado a Enviada
        $estadoEnviada = EstadoTransaccion::where('descripcionET', 'Enviada')->first();
        $compra->update([
            'estado_transaccion_id' => $estadoEnviada->id,
            'fecha_envio' => now()
        ]);

        // Por ahora solo simular envío (sin email)
        return redirect()->route('compras.index')->with('success', 'Orden marcada como enviada. Ya puede proceder a cotizar.');
        
        /* TEMPORALMENTE DESHABILITADO - Email functionality
        // Enviar email con el PDF al proveedor
        try {
            Mail::to($compra->proveedor->emailProv)->send(new OrdenCompraEnviada($compra));
            return redirect()->route('compras.index')->with('success', 'Orden enviada al proveedor por correo: ' . $compra->proveedor->emailProv);
        } catch (\Exception $e) {
            // Log del error para debugging
            Log::error('Error al enviar email de orden de compra: ' . $e->getMessage());
            return redirect()->route('compras.index')->with('error', 'Error al enviar el correo: ' . $e->getMessage());
        }
        */
    }

    // Mostrar formulario de cotización
    public function mostrarCotizar($compraId)
    {
        $compra = Compra::with(['detalles.producto', 'detalles.talla', 'proveedor'])->findOrFail($compraId);
        
        // Verificar que esté en estado Enviada
        if ($compra->estadoTransaccion->descripcionET !== 'Enviada') {
            return redirect()->route('compras.index')->with('error', 'Solo se pueden cotizar órdenes en estado Enviada.');
        }

        return view('Compra.cotizar', compact('compra'));
    }

    // Guardar cotización del proveedor
    public function guardarCotizacion(Request $request, $compraId)
    {
        $compra = Compra::with('detalles.producto')->findOrFail($compraId);
        
        $request->validate([
            'precio_cotizado' => 'required|array',
            'precio_cotizado.*' => 'required|numeric|min:0',
            'notas_proveedor' => 'nullable|string',
            'pdf_cotizacion' => 'required|file|mimes:pdf|max:10240',
            'descuento' => 'nullable|numeric|min:0'
        ]);

        $subtotal = 0;
        
        // Actualizar precios cotizados en cada detalle
        foreach ($compra->detalles as $detalle) {
            $precioCotizado = $request->precio_cotizado[$detalle->id];
            $subtotalLinea = $precioCotizado * $detalle->cantidad;
            
            $detalle->update([
                'precio_cotizado' => $precioCotizado,
                'precio_final' => $precioCotizado,
                'subtotal_linea' => $subtotalLinea
            ]);
            
            $subtotal += $subtotalLinea;
        }

        // Calcular totales
        $descuento = $request->descuento ?? 0;
        $subtotalConDescuento = $subtotal - $descuento;
        $igv = $subtotalConDescuento * 0.18;
        $total = $subtotalConDescuento + $igv;

        // Subir PDF de cotización (obligatorio)
        $file = $request->file('pdf_cotizacion');
        $filename = 'cotizacion_' . $compra->codigoCompra . '_' . time() . '.pdf';
        $pdfPath = $file->storeAs('cotizaciones', $filename, 'public');

        // Cambiar estado a Cotizada (siempre pago contra entrega)
        $estadoCotizada = EstadoTransaccion::where('descripcionET', 'Cotizada')->first();
        $compra->update([
            'estado_transaccion_id' => $estadoCotizada->id,
            'fecha_cotizacion' => now(),
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'igv' => $igv,
            'total' => $total,
            'condiciones_pago' => 'Pago contra entrega',
            'notas_proveedor' => $request->notas_proveedor,
            'pdf_cotizacion' => $pdfPath
        ]);
        
        return redirect()->route('compras.index')->with('success', 'Cotización guardada correctamente. Pago: Contra entrega');
    }

    // Aprobar cotización
    public function aprobarCompra($compraId)
    {
        $compra = Compra::findOrFail($compraId);
        
        if ($compra->estadoTransaccion->descripcionET !== 'Cotizada') {
            return redirect()->route('compras.index')->with('error', 'Solo se pueden aprobar órdenes en estado Cotizada.');
        }

        $estadoAprobada = EstadoTransaccion::where('descripcionET', 'Aprobada')->first();
        $compra->update([
            'estado_transaccion_id' => $estadoAprobada->id,
            'fecha_aprobacion' => now()
        ]);

        return redirect()->route('compras.index')->with('success', 'Cotización aprobada correctamente.');
    }
}

