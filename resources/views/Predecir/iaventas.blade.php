@extends('adminlte::page')

@section('title', 'Predicción de Ventas')

@section('content_header')
@stop

@section('content')
    <iframe src="http://localhost:8501" width="100%" height="1000" frameborder="0"></iframe>
    {{-- <iframe src="https://eliboutique.streamlit.app/" width="100%" height="1000" frameborder="0"></iframe> --}}
@stop

