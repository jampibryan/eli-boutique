<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrediccionController extends Controller
{
    public function index()
    {
        return view('predecir.iaventas');
    }
}