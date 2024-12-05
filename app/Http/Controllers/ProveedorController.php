<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedor;
use App\Models\Proveedor;
use App\Models\TipoProveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ProveedorController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware para verificar permisos
        $this->middleware('permission:gestionar proveedores', ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);
    }
    
    public function apiProveedores()
    {
        // Obtener todos los proveedores
        $proveedores = Proveedor::all();

        // Retornar los proveedores en formato JSON
        return response()->json($proveedores);
    }
    

    public function pdfProveedores()
    {
        $proveedores = Proveedor::whereNotNull('id')->get();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Proveedor.reporte', compact('proveedores')));

        // return $pdf->download(); //Descarga automática
        return $pdf->stream('Reporte de Proveedores.pdf'); //Abre una pestaña
    }

    public function index()
    {
        $proveedores = Proveedor::all();

        return view('Proveedor.index', compact('proveedores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tiposProv = TipoProveedor::all();

        return view('Proveedor.create', compact('tiposProv'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProveedor $request)
    {
        $proveedor = Proveedor::create($request->all());

        return redirect()->route('proveedores.index');
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
    public function edit(Proveedor $proveedore)
    {
        $tiposProv = TipoProveedor::all();
        $proveedor = $proveedore;
        return view('Proveedor.edit', compact('proveedor', 'tiposProv'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProveedor $request, Proveedor $proveedore)
    {
        $proveedore->update($request->all());
        
        return redirect()->route('proveedores.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proveedor $proveedore)
    {
        $proveedore->delete();
        return redirect()->route('proveedores.index');
    }
}