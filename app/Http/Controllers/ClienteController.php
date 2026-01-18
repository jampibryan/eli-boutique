<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCliente;
use App\Models\Cliente;
use App\Models\TipoGenero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        // Aplicar middleware para verificar permisos
        $this->middleware('permission:gestionar clientes', ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);
    }
    
    public function apiClientes()
    {
        $clientes = Cliente::with('tipoGenero')->get();
        return response()->json($clientes);
    }

    public function pdfClientes()
    {
        // $clientes = Cliente::whereNotNull('id')->orderBy('apellidoCliente')->get();
        $clientes = Cliente::whereNotNull('id')->get();


        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Cliente.reporte', compact('clientes')));

        // return $pdf->download(); //Descarga automática
        return $pdf->stream('Reporte de Clientes.pdf'); //Abre una pestaña
    }

    public function index()
    {
        $clientes = Cliente::all();

        // return view('Cliente.index', ['clientes' => $clientes]);
        // Es lo mismo
        return view('Cliente.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $generos = TipoGenero::all();
        $redirect = request('redirect');

        return view('Cliente.create', compact('generos', 'redirect'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCliente $request)
    {
        $cliente = Cliente::create($request->all());

        // Guardar el cliente en sesión para preseleccionarlo
        session(['venta_cliente' => $cliente->id]);

        if ($request->has('redirect') && $request->redirect === 'ventas.create') {
            return redirect()->route('ventas.create')->with('success', 'Cliente registrado exitosamente. Ahora puedes proceder con la venta.');
        }

        if ($request->has('redirect') && $request->redirect === 'ventas.edit') {
            $ventaId = $request->input('venta_id');
            return redirect()->route('ventas.edit', $ventaId)->with('success', 'Cliente registrado exitosamente.');
        }

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
        return view('Cliente.edit', compact('cliente', 'generos'));
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
        $cliente->delete(); // Soft delete: solo marca como eliminado sin borrar físicamente
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
    }

}
