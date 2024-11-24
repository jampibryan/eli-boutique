<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColaborador;
use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\TipoGenero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ColaboradorController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware para verificar permisos
        $this->middleware('permission:gestionar colaboradores', ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);
    }

    public function pdfColaboradores()
    {
        $colaboradores = Colaborador::whereNotNull('id')->get();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('colaborador.reporte', compact('colaboradores')));

        // return $pdf->download(); //Descarga automática
        return $pdf->stream('Reporte de Colaboradores.pdf'); //Abre una pestaña
    }

    public function index()
    {
        $colaboradores = Colaborador::all();

        return view('colaborador.index', compact('colaboradores'));
    }

    public function create()
    {
        $cargos = Cargo::all();
        $generos = TipoGenero::all();

        return view('colaborador.create', compact('cargos', 'generos'));
    }


    public function store(StoreColaborador $request)
    {
        $colaborador = Colaborador::create($request->all());

        return redirect()->route('colaboradores.index');
    }

 
    public function show(string $id)
    {
        //
    }

    public function edit(Colaborador $colaboradore)
    {
        $cargos = Cargo::all();
        $generos = TipoGenero::all();
        $colaborador = $colaboradore;
        return view('colaborador.edit', compact('colaborador', 'cargos', 'generos'));
    }

 
    public function update(StoreColaborador $request, Colaborador $colaboradore)
    {
        $colaboradore->update($request->all());
        
        return redirect()->route('colaboradores.index');
    }


    public function destroy(Colaborador $colaboradore)
    {
        $colaboradore->delete();
        return redirect()->route('colaboradores.index');
    }
}
