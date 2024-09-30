<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedor;
use App\Models\Proveedor;
use App\Models\TipoProveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware para verificar permisos
        $this->middleware('permission:gestionar proveedores', ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);
    }

    public function index()
    {
        $proveedores = Proveedor::all();

        return view('proveedor.index', compact('proveedores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tiposProv = TipoProveedor::all();

        return view('proveedor.create', compact('tiposProv'));
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
        return view('proveedor.edit', compact('proveedor', 'tiposProv'));
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