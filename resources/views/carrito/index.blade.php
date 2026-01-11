@extends('adminlte::page')

@section('title', 'Carrito de Compras')

@section('content_header')
    <h1>Carrito de Compras</h1>
@stop

@section('content')
    <a href="{{ route('productos.index') }}" class="btn btn-secondary mb-3">Seguir Comprando</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(empty($carrito))
        <p>Tu carrito está vacío.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Talla</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach($carrito as $index => $item)
                    @php
                        $producto = $productos[$item['producto_id']];
                        $talla = $tallas[$item['talla_id']];
                        $subtotal = $item['cantidad'] * $producto->precioP;
                        $total += $subtotal;
                    @endphp
                    <tr>
                        <td>{{ $producto->descripcionP }}</td>
                        <td>{{ $talla->descripcion }}</td>
                        <td>{{ $item['cantidad'] }}</td>
                        <td>S/. {{ number_format($producto->precioP, 2) }}</td>
                        <td>S/. {{ number_format($subtotal, 2) }}</td>
                        <td>
                            <form action="{{ route('carrito.remover', $index) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Remover</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h4>Total: S/. {{ number_format($total, 2) }}</h4>

        <a href="{{ route('ventas.create') }}" class="btn btn-success">Continuar con la Venta</a>
    @endif
@stop