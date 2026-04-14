<?php

namespace App\Http\Controllers;

use App\Mail\OrdenCompraEnviada;
use App\Models\Colaborador;
use App\Models\Compra;
use App\Models\EstadoTransaccion;
use App\Models\Producto;
use App\Models\ProductoTalla;
use App\Models\Proveedor;
use App\Services\CompraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CompraController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:gestionar compras', ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);
    }

    public function apiCompras()
    {
        $compras = Compra::with(['proveedor', 'detalles', 'pago'])->get();

        return view('Compra.index', compact('compras'));
    }

    public function pdfCompras()
    {
        $compras = Compra::with(['proveedor', 'detalles.producto.categoriaProducto', 'comprobante', 'estadoTransaccion'])->get();
        $totalesPorCategoria = [];

        foreach ($compras as $compra) {
            foreach ($compra->detalles as $detalle) {
                $categoria = $detalle->producto->categoriaProducto->nombreCP ?? 'Sin categoria';

                if (!isset($totalesPorCategoria[$categoria])) {
                    $totalesPorCategoria[$categoria] = ['cantidad' => 0, 'monto' => 0];
                }

                $totalesPorCategoria[$categoria]['cantidad'] += $detalle->cantidad;
                $totalesPorCategoria[$categoria]['monto'] += $detalle->subtotal_linea;
            }
        }

        arsort($totalesPorCategoria);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Compra.reporte', compact('compras', 'totalesPorCategoria')));

        return $pdf->stream('Reporte de Compras.pdf');
    }

    public function pdfOrdenCompra(Compra $compra)
    {
        $compra->load(['proveedor', 'detalles.producto', 'detalles.talla', 'comprobante', 'estadoTransaccion']);
        $colaborador = Colaborador::find(1);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Compra.orden', compact('compra', 'colaborador')));

        return $pdf->stream('Orden de compra - ' . $compra->codigoCompra . '.pdf');
    }

    public function index(Request $request)
    {
        $query = Compra::with(['proveedor', 'detalles.producto', 'detalles.talla', 'pago', 'estadoTransaccion', 'comprobante']);

        if ($request->filled('search')) {
            $query->where('codigoCompra', 'like', "%{$request->search}%");
        }

        if ($request->filled('estado')) {
            $query->whereHas('estadoTransaccion', function ($q) use ($request) {
                $q->where('descripcionET', $request->estado);
            });
        }

        $orden = $request->get('orden', 'reciente');
        $query->orderBy('id', $orden === 'reciente' ? 'desc' : 'asc');

        $compras = $query->paginate(6)->appends($request->query());

        return view('Compra.index', compact('compras'));
    }

    public function create()
    {
        $proveedores = Proveedor::withoutTrashed()->get();
        $productos = Producto::withoutTrashed()->with(['tallaStocks.talla'])->get();
        $tallas = ProductoTalla::all();

        return view('Compra.create', compact('proveedores', 'productos', 'tallas'));
    }

    public function store(Request $request, CompraService $compraService)
    {
        $validated = $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.talla_id' => 'required|exists:producto_tallas,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $compraService->create($validated);

        return redirect()->route('compras.index')->with('success', 'Orden de compra creada. Estado: Borrador');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Compra $compra)
    {
        $compra->load(['detalles.producto.tallaStocks.talla', 'detalles.talla']);
        $proveedores = Proveedor::withoutTrashed()->get();
        $productos = Producto::withoutTrashed()->with(['tallaStocks.talla'])->get();
        $tallas = ProductoTalla::all();

        return view('Compra.edit', compact('compra', 'proveedores', 'productos', 'tallas'));
    }

    public function update(Request $request, Compra $compra, CompraService $compraService)
    {
        $validated = $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.talla_id' => 'required|exists:producto_tallas,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $compraService->update($compra, $validated);

        return redirect()->route('compras.index')->with('success', 'Compra actualizada con exito.');
    }

    public function destroy(Compra $compra)
    {
        $compra->delete();

        return redirect()->route('compras.index')->with('success', 'Compra eliminada con exito.');
    }

    public function recibirPedido($compraId, CompraService $compraService)
    {
        $compraService->receive($compraId);

        return redirect()->route('compras.index')->with('success', 'Mercaderia recibida y stock actualizado. Ahora puede proceder a pagar al proveedor.');
    }

    public function anularCompra($compraId)
    {
        $compra = Compra::findOrFail($compraId);

        if ($compra->estadoTransaccion->descripcionET !== 'Anulado') {
            $compra->anular();

            return redirect()->route('compras.index')->with('success', 'Compra anulada correctamente.');
        }

        return redirect()->route('compras.index')->with('error', 'La compra ya esta anulada.');
    }

    public function enviarCompra($compraId)
    {
        $compra = Compra::with(['detalles.producto', 'detalles.talla', 'proveedor', 'comprobante', 'estadoTransaccion'])->findOrFail($compraId);

        if ($compra->estadoTransaccion->descripcionET !== 'Borrador') {
            return redirect()->route('compras.index')->with('error', 'Solo se pueden enviar ordenes en estado Borrador.');
        }

        $estadoEnviada = EstadoTransaccion::where('descripcionET', 'Enviada')->first();

        $compra->update([
            'estado_transaccion_id' => $estadoEnviada->id,
            'fecha_envio' => now(),
        ]);

        return redirect()->route('compras.index')->with('success', 'Orden marcada como enviada. Ya puede proceder a cotizar.');

        /* TEMPORALMENTE DESHABILITADO - Email functionality
        try {
            Mail::to($compra->proveedor->emailProv)->send(new OrdenCompraEnviada($compra));
            return redirect()->route('compras.index')->with('success', 'Orden enviada al proveedor por correo: ' . $compra->proveedor->emailProv);
        } catch (\Exception $e) {
            Log::error('Error al enviar email de orden de compra: ' . $e->getMessage());
            return redirect()->route('compras.index')->with('error', 'Error al enviar el correo: ' . $e->getMessage());
        }
        */
    }

    public function mostrarCotizar($compraId)
    {
        $compra = Compra::with(['detalles.producto', 'detalles.talla', 'proveedor'])->findOrFail($compraId);

        if ($compra->estadoTransaccion->descripcionET !== 'Enviada') {
            return redirect()->route('compras.index')->with('error', 'Solo se pueden cotizar ordenes en estado Enviada.');
        }

        return view('Compra.cotizar', compact('compra'));
    }

    public function guardarCotizacion(Request $request, $compraId)
    {
        $compra = Compra::with('detalles.producto')->findOrFail($compraId);

        $request->validate([
            'precio_cotizado' => 'required|array',
            'precio_cotizado.*' => 'required|numeric|min:0',
            'notas_proveedor' => 'nullable|string',
            'pdf_cotizacion' => 'nullable|file|mimes:pdf|max:10240',
            'descuento' => 'nullable|numeric|min:0',
        ]);

        $pdfPath = $compra->pdf_cotizacion;
        $uploadedPdfPath = null;

        if ($request->hasFile('pdf_cotizacion')) {
            $file = $request->file('pdf_cotizacion');
            $filename = 'cotizacion_' . $compra->codigoCompra . '_' . time() . '.pdf';
            $uploadedPdfPath = $file->storeAs('cotizaciones', $filename, 'public');
            $pdfPath = $uploadedPdfPath;
        }

        try {
            DB::transaction(function () use ($request, $compra, $pdfPath) {
                $subtotal = 0;

                foreach ($compra->detalles as $detalle) {
                    $precioCotizado = $request->precio_cotizado[$detalle->id];
                    $subtotalLinea = $precioCotizado * $detalle->cantidad;

                    $detalle->update([
                        'precio_cotizado' => $precioCotizado,
                        'precio_final' => $precioCotizado,
                        'subtotal_linea' => $subtotalLinea,
                    ]);

                    $subtotal += $subtotalLinea;
                }

                $descuento = $request->descuento ?? 0;
                $subtotalConDescuento = $subtotal - $descuento;
                $igv = $subtotalConDescuento * 0.18;
                $total = $subtotalConDescuento + $igv;
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
                    'pdf_cotizacion' => $pdfPath,
                ]);
            });
        } catch (\Throwable $exception) {
            if ($uploadedPdfPath) {
                Storage::disk('public')->delete($uploadedPdfPath);
            }

            throw $exception;
        }

        return redirect()->route('compras.index')->with('success', 'Cotizacion guardada correctamente. Pago: Contra entrega');
    }

    public function aprobarCompra($compraId)
    {
        $compra = Compra::findOrFail($compraId);

        if ($compra->estadoTransaccion->descripcionET !== 'Cotizada') {
            return redirect()->route('compras.index')->with('error', 'Solo se pueden aprobar ordenes en estado Cotizada.');
        }

        $estadoAprobada = EstadoTransaccion::where('descripcionET', 'Aprobada')->first();

        $compra->update([
            'estado_transaccion_id' => $estadoAprobada->id,
            'fecha_aprobacion' => now(),
        ]);

        return redirect()->route('compras.index')->with('success', 'Cotizacion aprobada correctamente.');
    }
}
