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

    public function pdfCompras(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $query = Compra::with(['proveedor', 'detalles.producto.categoriaProducto', 'comprobante', 'estadoTransaccion']);

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

        $compras = $query->get();
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
        $compra->load(['proveedor', 'detalles.producto', 'detalles.talla', 'comprobante', 'estadoTransaccion', 'colaborador']);
        $colaborador = $compra->colaborador ?? Colaborador::find(1);

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
        $compra->loadMissing('estadoTransaccion');

        if ($compra->estadoTransaccion && in_array($compra->estadoTransaccion->descripcionET, ['Recibida', 'Pagada'])) {
            return redirect()->route('compras.index')->with('error', 'No se puede eliminar una orden de compra que ya fue recibida o pagada. El stock e importes ya fueron procesados.');
        }

        $compra->delete();

        return redirect()->route('compras.index')->with('success', 'Compra eliminada con exito.');
    }

    public function recibirPedido($compraId, CompraService $compraService)
    {
        $compraService->receive($compraId);

        return redirect()->route('compras.index')->with('success', 'Mercaderia recibida y stock actualizado. Ahora puede proceder a pagar al proveedor.');
    }

    public function anularCompra($compraId, CompraService $compraService)
    {
        $compraService->anular($compraId);

        return redirect()->route('compras.index')->with('success', 'Compra anulada correctamente.');
    }

    public function enviarCompra($compraId, CompraService $compraService)
    {
        $compraService->send($compraId);

        return redirect()->route('compras.index')->with('success', 'Orden marcada como enviada. Ya puede proceder a cotizar.');
    }

    public function mostrarCotizar($compraId)
    {
        $compra = Compra::with(['detalles.producto', 'detalles.talla', 'proveedor'])->findOrFail($compraId);

        if ($compra->estadoTransaccion->descripcionET !== 'Enviada') {
            return redirect()->route('compras.index')->with('error', 'Solo se pueden cotizar ordenes en estado Enviada.');
        }

        return view('Compra.cotizar', compact('compra'));
    }

    public function guardarCotizacion(Request $request, $compraId, CompraService $compraService)
    {
        $validated = $request->validate([
            'precio_cotizado' => 'required|array',
            'precio_cotizado.*' => 'required|numeric|min:0',
            'notas_proveedor' => 'nullable|string',
            'pdf_cotizacion' => 'nullable|file|mimes:pdf|max:10240',
            'descuento' => 'nullable|numeric|min:0',
        ]);

        $uploadedPdfPath = null;
        if ($request->hasFile('pdf_cotizacion')) {
            $file = $request->file('pdf_cotizacion');
            $filename = 'cotizacion_' . $compraId . '_' . time() . '.pdf';
            $uploadedPdfPath = $file->storeAs('cotizaciones', $filename, 'public');
        }

        try {
            $compraService->saveQuote($compraId, $validated, $uploadedPdfPath);
        } catch (\Throwable $exception) {
            if ($uploadedPdfPath) {
                Storage::disk('public')->delete($uploadedPdfPath);
            }

            throw $exception;
        }

        return redirect()->route('compras.index')->with('success', 'Cotizacion guardada correctamente. Pago: Contra entrega');
    }

    public function aprobarCompra($compraId, CompraService $compraService)
    {
        $compraService->approve($compraId);

        return redirect()->route('compras.index')->with('success', 'Cotizacion aprobada correctamente.');
    }
}
