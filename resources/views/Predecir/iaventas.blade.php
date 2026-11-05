@extends('adminlte::page')

@section('title', 'Predicción de Ventas')

@section('content_header')
@stop

@section('content')
    <iframe src="{{ config('services.streamlit.url') }}" title="Ventas, inventario y planificación" width="100%" height="1000" frameborder="0"></iframe>
@stop

