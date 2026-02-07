<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProducto;
use App\Models\CategoriaProducto;
use App\Models\Producto;
use App\Models\ProductoGenero;
use App\Models\ProductoTalla;
use App\Models\ProductoTallaStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware para verificar permisos
        $this->middleware('permission:ver productos|gestionar productos', ['only' => ['index', 'show']]);
        $this->middleware('permission:gestionar productos', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
    }

    public function pdfProductos()
    {

        $productos = Producto::with(['categoriaProducto', 'tallaStocks.talla'])
            ->get()
            ->sortBy('categoriaProducto.nombreCP');

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Producto.reporte', compact('productos')));

        // return $pdf->download(); //Descarga automática
        return $pdf->stream('Reporte de Productos.pdf'); //Abre una pestaña
    }

    public function index(Request $request)
    {
        $categorias = CategoriaProducto::all();
        $categoriaId = $request->get('categoria'); // Obtener el id de la categoría desde la solicitud

        // Filtrar productos por la categoría seleccionada
        if ($categoriaId) {
            // Si hay filtro, solo traer productos de esa categoría ordenados alfabéticamente
            $productos = Producto::withoutTrashed()
                ->where('categoria_producto_id', $categoriaId)
                ->orderBy('descripcionP', 'asc')
                ->get();
        } else {
            $productos = Producto::withoutTrashed()
                ->with('categoriaProducto')
                ->get()
                ->sortBy([
                    ['categoriaProducto.id', 'asc'],
                    ['descripcionP', 'asc']
                ]);
        }

        $productos = $productos->load(['tallaStocks.talla'])->map(function ($producto) {
            $producto->stock_total = $producto->tallaStocks->sum('stock');
            return $producto;
        });

        $tallas = ProductoTalla::all();

        return view('Producto.index', compact('productos', 'categorias', 'tallas'));
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

    public function agregarAlCarrito(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
        ]);

        $producto = Producto::with('tallaStocks')->find($request->producto_id);

        // Obtener la primera talla CON STOCK disponible del producto
        $primeraTalla = $producto->tallaStocks->where('stock', '>', 0)->first();

        if (!$primeraTalla) {
            return response()->json([
                'success' => false,
                'error' => 'Este producto no tiene stock disponible en ninguna talla.'
            ], 400);
        }

        $tallaId = $primeraTalla->producto_talla_id;
        $cantidad = 1; // Cantidad por defecto

        // Obtener carrito actual de la sesión
        $carrito = session()->get('carrito', []);

        // Calcular cantidad total que ya existe en el carrito para este producto y talla
        $cantidadEnCarrito = 0;
        foreach ($carrito as $item) {
            if ($item['producto_id'] == $request->producto_id && $item['talla_id'] == $tallaId) {
                $cantidadEnCarrito += $item['cantidad'];
            }
        }

        // Verificar stock considerando lo que ya está en el carrito
        $stockDisponible = $primeraTalla->stock;
        $cantidadTotalRequerida = $cantidadEnCarrito + $cantidad;

        if ($cantidadTotalRequerida > $stockDisponible) {
            return response()->json([
                'success' => false,
                'error' => "Stock insuficiente para {$producto->descripcionP} talla {$primeraTalla->talla->descripcion}. Stock disponible: {$stockDisponible}, ya en carrito: {$cantidadEnCarrito}"
            ], 400);
        }

        // Buscar si ya existe el mismo producto con la misma talla
        $encontrado = false;
        $indiceEncontrado = -1;

        foreach ($carrito as $index => $item) {
            if ($item['producto_id'] == $request->producto_id && $item['talla_id'] == $tallaId) {
                $encontrado = true;
                $indiceEncontrado = $index;
                break;
            }
        }

        // Si se encontró, sumar la cantidad
        if ($encontrado) {
            $carrito[$indiceEncontrado]['cantidad'] += $cantidad;
        } else {
            // Si no se encontró, agregar como nuevo item
            $carrito[] = [
                'producto_id' => $request->producto_id,
                'talla_id' => $tallaId,
                'cantidad' => $cantidad,
            ];
        }

        // Guardar en sesión
        session()->put('carrito', $carrito);

        $cantidadTotal = $carrito[$indiceEncontrado >= 0 ? $indiceEncontrado : count($carrito) - 1]['cantidad'];

        return response()->json([
            'success' => true,
            'message' => "Producto agregado al carrito. Total: {$cantidadTotal} unidades.",
            'cantidad_items' => count($carrito)
        ]);
    }

    public function verCarrito()
    {
        $carrito = session()->get('carrito', []);
        $productos = Producto::with('tallaStocks.talla')
            ->whereIn('id', collect($carrito)->pluck('producto_id'))
            ->get()
            ->keyBy('id');
        $tallas = ProductoTalla::whereIn('id', collect($carrito)->pluck('talla_id'))->get()->keyBy('id');

        // Crear un array con el stock específico de cada producto-talla
        $stocksPorTalla = [];
        foreach ($productos as $producto) {
            foreach ($producto->tallaStocks as $tallaStock) {
                $key = $producto->id . '_' . $tallaStock->producto_talla_id;
                $stocksPorTalla[$key] = $tallaStock->stock;
            }
        }

        // Verificar qué productos pueden duplicarse (tienen tallas disponibles que no están en el carrito)
        $puedenDuplicarse = [];
        foreach ($carrito as $index => $item) {
            $productoId = $item['producto_id'];

            if (!isset($puedenDuplicarse[$productoId])) {
                // Obtener tallas con stock de este producto
                $producto = $productos[$productoId];
                $tallasConStock = $producto->tallaStocks->where('stock', '>', 0)->pluck('producto_talla_id')->toArray();

                // Obtener tallas que ya están en el carrito
                $tallasEnCarrito = collect($carrito)
                    ->where('producto_id', $productoId)
                    ->pluck('talla_id')
                    ->toArray();

                // Verificar si hay tallas disponibles que no estén en el carrito
                $tallasDisponibles = array_diff($tallasConStock, $tallasEnCarrito);
                $puedenDuplicarse[$productoId] = !empty($tallasDisponibles);
            }
        }

        return view('Carrito.index', compact('carrito', 'productos', 'tallas', 'stocksPorTalla', 'puedenDuplicarse'));
    }

    public function removerDelCarrito($index)
    {
        $carrito = session()->get('carrito', []);
        if (isset($carrito[$index])) {
            unset($carrito[$index]);
            $carrito = array_values($carrito); // Reindexar
            session()->put('carrito', $carrito);
        }

        return redirect()->back()->with('success', 'Producto removido del carrito.');
    }

    public function actualizarCantidadCarrito(Request $request, $index)
    {
        $carrito = session()->get('carrito', []);

        if (!isset($carrito[$index])) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado en el carrito.']);
        }

        $accion = $request->input('accion'); // 'aumentar' o 'disminuir'
        $item = $carrito[$index];

        // Obtener el stock de esta talla específica
        $tallaStock = ProductoTallaStock::where('producto_id', $item['producto_id'])
            ->where('producto_talla_id', $item['talla_id'])
            ->first();

        if (!$tallaStock) {
            return response()->json(['success' => false, 'message' => 'Talla no encontrada.']);
        }

        // Calcular cantidad total de este producto+talla en el carrito
        $cantidadEnCarritoMismaTalla = 0;
        foreach ($carrito as $carritoItem) {
            if ($carritoItem['producto_id'] == $item['producto_id'] && $carritoItem['talla_id'] == $item['talla_id']) {
                $cantidadEnCarritoMismaTalla += $carritoItem['cantidad'];
            }
        }

        if ($accion === 'aumentar') {
            // Validar que la cantidad actual del item no exceda el stock
            if ($carrito[$index]['cantidad'] >= $tallaStock->stock) {
                return response()->json(['success' => false]);
            }

            // Validar stock antes de aumentar
            $nuevaCantidadTotal = $cantidadEnCarritoMismaTalla + 1;

            if ($nuevaCantidadTotal > $tallaStock->stock) {
                return response()->json(['success' => false]);
            }

            $carrito[$index]['cantidad'] += 1;
            session()->put('carrito', $carrito);

            return response()->json([
                'success' => true,
                'cantidad' => $carrito[$index]['cantidad'],
                'message' => "Cantidad aumentada a {$carrito[$index]['cantidad']} unidades."
            ]);
        } elseif ($accion === 'disminuir') {
            if ($carrito[$index]['cantidad'] > 1) {
                $carrito[$index]['cantidad'] -= 1;
                session()->put('carrito', $carrito);

                return response()->json([
                    'success' => true,
                    'cantidad' => $carrito[$index]['cantidad'],
                    'message' => "Cantidad actualizada a {$carrito[$index]['cantidad']} unidades."
                ]);
            } else {
                return response()->json(['success' => false]);
            }
        }

        return response()->json(['success' => false]);
    }

    public function cambiarTallaCarrito(Request $request, $index)
    {
        $carrito = session()->get('carrito', []);

        if (!isset($carrito[$index])) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado en el carrito.']);
        }

        $accion = $request->input('accion'); // 'anterior' o 'siguiente'
        $item = $carrito[$index];

        // Obtener todas las tallas disponibles del producto
        $producto = Producto::with('tallaStocks.talla')->find($item['producto_id']);

        if (!$producto || $producto->tallaStocks->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Producto sin tallas disponibles.']);
        }

        // Obtener array de IDs de tallas que tienen stock (ordenadas)
        $tallasConStock = $producto->tallaStocks
            ->where('stock', '>', 0)
            ->sortBy('producto_talla_id')
            ->pluck('producto_talla_id')
            ->toArray();

        // Obtener tallas que YA están ocupadas en el carrito por OTROS items del mismo producto
        $tallasOcupadas = [];
        foreach ($carrito as $idx => $carritoItem) {
            // Excluir el item actual que estamos editando
            if ($idx != $index && $carritoItem['producto_id'] == $item['producto_id']) {
                $tallasOcupadas[] = $carritoItem['talla_id'];
            }
        }

        // Filtrar: solo tallas con stock que NO estén ocupadas
        $tallasLibres = array_diff($tallasConStock, $tallasOcupadas);

        // Siempre incluir la talla actual aunque esté en la lista
        if (!in_array($item['talla_id'], $tallasLibres)) {
            $tallasLibres[] = $item['talla_id'];
        }

        // Reordenar y reindexar
        $tallasDisponibles = array_values(array_unique($tallasLibres));
        sort($tallasDisponibles); // Ordenar numéricamente

        // Si solo hay una talla disponible (la actual), no se puede cambiar
        if (count($tallasDisponibles) <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'No hay otras tallas disponibles. Todas están ocupadas en el carrito.'
            ]);
        }

        // Encontrar índice de la talla actual
        $indiceActual = array_search($item['talla_id'], $tallasDisponibles);

        if ($indiceActual === false) {
            return response()->json(['success' => false, 'message' => 'Talla actual no válida.']);
        }

        // Calcular nueva talla según acción (ciclar solo entre tallas disponibles)
        $nuevoIndice = $indiceActual;
        if ($accion === 'siguiente') {
            $nuevoIndice = ($indiceActual + 1) % count($tallasDisponibles);
        } elseif ($accion === 'anterior') {
            $nuevoIndice = ($indiceActual - 1 + count($tallasDisponibles)) % count($tallasDisponibles);
        }

        $nuevaTallaId = $tallasDisponibles[$nuevoIndice];

        // Obtener stock de la nueva talla
        $tallaStock = $producto->tallaStocks->firstWhere('producto_talla_id', $nuevaTallaId);

        if (!$tallaStock) {
            return response()->json(['success' => false, 'message' => 'Stock de talla no encontrado.']);
        }

        // Validar que la cantidad actual no exceda el stock de la nueva talla
        if ($item['cantidad'] > $tallaStock->stock) {
            // Ajustar cantidad al stock disponible
            $carrito[$index]['cantidad'] = $tallaStock->stock;
        }

        // Cambiar la talla
        $carrito[$index]['talla_id'] = $nuevaTallaId;
        session()->put('carrito', $carrito);

        return response()->json([
            'success' => true,
            'talla_nombre' => $tallaStock->talla->descripcion,
            'talla_id' => $nuevaTallaId,
            'stock' => $tallaStock->stock,
            'cantidad' => $carrito[$index]['cantidad'],
            'message' => "Talla cambiada a {$tallaStock->talla->descripcion}"
        ]);
    }

    public function duplicarItemCarrito($index)
    {
        $carrito = session()->get('carrito', []);

        if (!isset($carrito[$index])) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado en el carrito.']);
        }

        // Obtener el item a duplicar
        $itemOriginal = $carrito[$index];
        $productoId = $itemOriginal['producto_id'];

        // Obtener todas las tallas disponibles del producto ordenadas
        $producto = Producto::with('tallaStocks.talla')->find($productoId);
        if (!$producto) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado.']);
        }

        // Obtener tallas que tienen stock, ordenadas por su ID
        $tallasConStock = $producto->tallaStocks
            ->where('stock', '>', 0)
            ->sortBy('producto_talla_id')
            ->pluck('producto_talla_id')
            ->toArray();

        // Obtener tallas que YA están en el carrito para este producto
        $tallasEnCarrito = [];
        foreach ($carrito as $item) {
            if ($item['producto_id'] == $productoId) {
                $tallasEnCarrito[] = $item['talla_id'];
            }
        }

        // Buscar la siguiente talla disponible que NO esté en el carrito (ordenadas)
        $tallasDisponibles = array_values(array_diff($tallasConStock, $tallasEnCarrito));

        if (empty($tallasDisponibles)) {
            return response()->json([
                'success' => false,
                'message' => 'Ya tienes todas las tallas disponibles de este producto en el carrito.'
            ]);
        }

        // Tomar la primera talla disponible (en orden)
        $nuevaTallaId = $tallasDisponibles[0];

        // Crear una copia del item con la nueva talla
        $itemDuplicado = [
            'producto_id' => $productoId,
            'talla_id' => $nuevaTallaId,
            'cantidad' => 1,
        ];

        // Agregar el duplicado al final del carrito
        $carrito[] = $itemDuplicado;
        session()->put('carrito', $carrito);

        return response()->json([
            'success' => true,
            'message' => 'Producto duplicado con siguiente talla disponible.',
            'nuevo_index' => count($carrito) - 1
        ]);
    }
}
