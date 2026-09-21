<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProducto;
use App\Models\CategoriaProducto;
use App\Models\Producto;
use App\Models\ProductoGenero;
use App\Models\ProductoTalla;
use App\Models\ProductoTallaStock;
use App\Services\CarritoService;
use App\Services\ProductoExcelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware para verificar permisos
        $this->middleware('permission:ver productos|gestionar productos', ['only' => ['index', 'show', 'pdfProductos', 'excelProductos']]);
        $this->middleware('permission:gestionar productos', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
    }

    public function excelProductos(ProductoExcelService $excelService)
    {
        $filePath = $excelService->generarExcel();
        $fileName = 'Reporte_Productos_Eli_Boutique_' . date('Y-m-d') . '.xlsx';

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function pdfProductos()
    {
        $productos = Producto::with(['categoriaProducto', 'productoGenero', 'tallaStocks.talla'])
            ->get()
            ->sortBy('categoriaProducto.nombreCP');

        // Calcular stock total por categoría
        $stockPorCategoria = [];
        foreach ($productos as $producto) {
            $categoria = $producto->categoriaProducto->nombreCP ?? 'Sin categoría';
            if (!isset($stockPorCategoria[$categoria])) {
                $stockPorCategoria[$categoria] = ['stock' => 0, 'productos' => 0, 'valorInventario' => 0];
            }
            $stockProducto = $producto->tallaStocks->sum('stock');
            $stockPorCategoria[$categoria]['stock'] += $stockProducto;
            $stockPorCategoria[$categoria]['productos']++;
            $stockPorCategoria[$categoria]['valorInventario'] += $stockProducto * $producto->precioP;
        }
        arsort($stockPorCategoria);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Producto.reporte', compact('productos', 'stockPorCategoria')));

        return $pdf->stream('Reporte de Productos.pdf');
    }

    public function index(Request $request)
    {
        $categorias = CategoriaProducto::all();
        $categoriaId = $request->get('categoria');

        $query = Producto::withoutTrashed()->with(['categoriaProducto', 'tallaStocks.talla']);

        if ($categoriaId) {
            $query->where('categoria_producto_id', $categoriaId);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('descripcionP', 'like', "%{$search}%")
                  ->orWhere('codigoP', 'like', "%{$search}%");
            });
        }

        $query->orderBy('categoria_producto_id', 'asc')->orderBy('descripcionP', 'asc');

        // Calcular stock total global de forma directa y agregada en la BD
        $productIdsQuery = (clone $query)->reorder()->select('id');
        $totalStock = (int) ProductoTallaStock::whereIn('producto_id', $productIdsQuery)->sum('stock');

        $productos = $query->paginate(12)->appends($request->query());

        // Calcular stock_total para cada producto de la página actual
        foreach ($productos as $producto) {
            $producto->stock_total = $producto->tallaStocks->sum('stock');
        }

        $tallas = ProductoTalla::all();

        return view('Producto.index', compact('productos', 'categorias', 'tallas', 'totalStock'));
    }

    public function create()
    {
        $categorias = CategoriaProducto::all();
        $tallas = ProductoTalla::all();
        $generos = ProductoGenero::all();
        return view('Producto.create', compact('categorias', 'tallas', 'generos'));
    }

    public function store(StoreProducto $request)
    {
        $path = null;
        if ($request->hasFile('imagenP')) {
            $file = $request->file('imagenP');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/productos'), $filename);
            $path = '/img/productos/' . $filename;
        }

        $producto = Producto::create([
            'codigoP' => $request->codigoP,
            'categoria_producto_id' => $request->categoria_producto_id,
            'producto_genero_id' => $request->producto_genero_id,
            'imagenP' => $path,
            'descripcionP' => $request->descripcionP,
            'precioP' => $request->precioP,
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto registrado correctamente.');
    }


    public function show(string $id)
    {
        $producto = Producto::find($id);

        return view('Productos.show', compact('producto'));
    }


    public function edit(Producto $producto)
    {
        $categorias = CategoriaProducto::all();
        $tallas = ProductoTalla::all();
        return view('Producto.edit', compact('producto', 'categorias', 'tallas'));
    }


    public function update(StoreProducto $request, Producto $producto)
    {
        if ($request->hasFile('imagenP')) {
            if ($producto->imagenP && file_exists(public_path($producto->imagenP))) {
                unlink(public_path($producto->imagenP));
                Log::info('Imagen eliminada: ' . $producto->imagenP);
            }
            $file = $request->file('imagenP');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/productos'), $filename);
            $producto->imagenP = '/img/productos/' . $filename;
        }

        $producto->update($request->only(['codigoP', 'categoria_producto_id', 'producto_genero_id', 'producto_talla_id', 'descripcionP', 'precioP', 'stockP']));

        return redirect()->route('productos.index');
    }


    public function destroy(Producto $producto)
    {
        $producto->delete(); // Soft delete: solo marca como eliminado sin borrar físicamente
        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }

    public function agregarAlCarrito(Request $request, CarritoService $carritoService)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
        ]);

        $resultado = $carritoService->addItem((int) $request->producto_id, 1);

        if (!$resultado['success']) {
            return response()->json($resultado, 400);
        }

        return response()->json($resultado);
    }

    public function verCarrito(CarritoService $carritoService)
    {
        $data = $carritoService->getCartViewData();

        return view('Carrito.index', $data);
    }

    public function removerDelCarrito($index, CarritoService $carritoService)
    {
        $carritoService->removeItem((int) $index);

        return redirect()->back()->with('success', 'Producto removido del carrito.');
    }

    public function actualizarCantidadCarrito(Request $request, $index, CarritoService $carritoService)
    {
        $resultado = $carritoService->updateQuantity((int) $index, (string) $request->input('accion'));

        return response()->json($resultado);
    }

    public function cambiarTallaCarrito(Request $request, $index, CarritoService $carritoService)
    {
        $resultado = $carritoService->changeTalla((int) $index, (string) $request->input('accion'));

        return response()->json($resultado);
    }

    public function duplicarItemCarrito($index, CarritoService $carritoService)
    {
        $resultado = $carritoService->duplicateItem((int) $index);

        return response()->json($resultado);
    }
}
