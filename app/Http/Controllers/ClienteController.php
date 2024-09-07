<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCliente;
use App\Models\Cliente;
use App\Models\TipoGenero;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::all();

        // return view('cliente.index', ['clientes' => $clientes]);
        // Es lo mismo
        return view('cliente.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $generos = TipoGenero::all();

        return view('cliente.create', compact('generos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCliente $request)
    {
        $cliente = Cliente::create($request->all());

        return redirect()->route('clientes.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        $generos = TipoGenero::all();
        return view('cliente.edit', compact('cliente', 'generos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCliente $request, Cliente $cliente)
    {
        $cliente->update($request->all());
        
        return redirect()->route('clientes.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index');
    }
}
