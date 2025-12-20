<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProducto;
use App\Models\CategoriaProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware para verificar permisos
        $this->middleware('permission:gestionar productos', ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);
    }
 
    public function pdfProductos()
    {
        // $productos = Producto::whereNotNull('id')->orderBy('categoriaProducto->nombreCP')->get();

        // Obtener los productos y ordenar por el nombre de la categoría relacionada
        $productos = Producto::with('categoriaProducto')
            ->get()
            ->sortBy('categoriaProducto.nombreCP'); // Ordena por la relación 'categoriaProducto'

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Producto.reporte', compact('productos')));

        // return $pdf->download(); //Descarga automática
        return $pdf->stream('Reporte de Productos.pdf'); //Abre una pestaña
    }

    public function index(Request $request)
    {
        // $productos = Producto::all();

        $categorias = CategoriaProducto::all(); // Cargar todas las categorías
        $categoriaId = $request->get('categoria'); // Obtener el id de la categoría desde la solicitud

        // Filtrar productos por la categoría seleccionada
        if ($categoriaId) {
            $productos = Producto::where('categoria_producto_id', $categoriaId)->get(); // Filtrar por categoria_producto_id
        } else {
            $productos = Producto::all(); // Cargar todos los productos si no se filtra
        }

        return view('Producto.index', compact('productos', 'categorias'));
    }


    // Muestra el formulario para crear un nuevo recurso. No hace cambios en la base de datos.
    public function create()
    {
        $categorias = CategoriaProducto::all();
        return view('Producto.create', compact('categorias'));
    }


    // Maneja la lógica para guardar el nuevo recurso en la base de datos después de que el formulario ha sido enviado.
    public function store(StoreProducto $request)
    {
        // Guardar la imagen en la carpeta 'public/productos' y obtener la ruta
        if ($request->hasFile('imagenP')) {
            $path = $request->file('imagenP')->store('productos', 'public');
        }

        // Crear el producto con los datos del formulario
        $producto = Producto::create([
            'codigoP' => $request->codigoP,
            'categoria_producto_id' => $request->categoria_producto_id,
            'imagenP' => $path, // Guardar la ruta de la imagen
            'descripcionP' => $request->descripcionP,
            'precioP' => $request->precioP,
            'stockP' => $request->stockP,
        ]);

        return redirect()->route('productos.index');
    }
    
 
    public function show(string $id)
    {
        $producto = Producto::find($id);

        return view('Productos.show', compact('producto'));
    }

 
    public function edit(Producto $producto)
    {
        $categorias = CategoriaProducto::all(); // Obtén todas las categorías para el formulario
        return view('Producto.edit', compact('producto', 'categorias'));
    }


    public function update(StoreProducto $request, Producto $producto)
    {
        
        // Verifica si se ha subido una nueva imagen
        if ($request->hasFile('imagenP')) {
            // Elimina la imagen anterior si existe
            if ($producto->imagenP && Storage::disk('public')->exists($producto->imagenP)) {
                Storage::disk('public')->delete($producto->imagenP);
                Log::info('Imagen eliminada: ' . $producto->imagenP);
            }

            // Guarda la nueva imagen y actualiza la ruta
            $producto->imagenP = $request->file('imagenP')->store('productos', 'public');
        }

        // Actualizar el resto de los campos
        // $producto->update($request->all());

        $producto->update($request->only(['codigoP', 'categoria_producto_id', 'descripcionP', 'precioP', 'stockP']));

        return redirect()->route('productos.index');
    }


    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index');
    }
}
