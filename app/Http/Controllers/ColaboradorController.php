<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColaborador;
use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\TipoGenero;
use Illuminate\Http\Request;

class ColaboradorController extends Controller
{

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
