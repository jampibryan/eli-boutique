@extends('adminlte::page')

@section('title', 'Gráficos')

@section('content_header')
    <h1>Reportes de Gráficos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body d-flex gap-2">
            <a href="{{ route('reporte.grafico.ventas') }}" class="btn btn-primary">Ventas</a>
            <a href="{{ route('reporte.grafico.compras') }}" class="btn btn-secondary">Compras</a>
            <a href="{{ route('tiempoReporteGrafico.pdf') }}" target="_blank" class="btn btn-dark">
                <i class="fas fa-clock"></i> Tiempo de Reporte Gráfico
            </a>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

@stop

@section('js')
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
@stop
