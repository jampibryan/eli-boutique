@extends('adminlte::page')

@section('title', 'Registrar Venta')

@section('content_header')
    <h1>Registro de Venta</h1>
@stop

@section('content')
    <a href="{{ route('ventas.index') }}" class="btn btn-secondary mb-3">Cancelar</a>

    <form action="{{ route('ventas.store') }}" method="POST" id="formVenta">
        @csrf

        <!-- Selección del Cliente -->
        <h4>Comprador</h4>
        <div class="form-group">
            {{-- <label for="cliente_id" class="col-form-label">Seleccionar Cliente</label> --}}
            <select class="form-control" name="cliente_id" required>
                <option value="">Seleccionar Cliente</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->nombreCliente }} {{ $cliente->apellidoCliente }}</option>
                @endforeach
            </select>
        </div>

        <!-- Detalle de Venta -->
        <h4>Seleccionar Productos</h4>
        <div id="detalleVenta" class="container-fluid">
            <!-- Producto base (template para clonar) -->
            <div class="form-group row productoRow" id="productoTemplate" style="justify-content: center; align-items: center; gap: 15px;">
                <!-- Agrupar el label y select del producto -->
                <div style="display: flex; align-items: center; gap: 5px;">
                    <label for="producto_id" class="col-form-label" id="productoLabel"></label>
                    <select class="form-control productoSelect" name="productos[0][id]" required>
                        <option value="">Seleccionar Producto</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}" data-precio="{{ $producto->precioP }}">
                                {{ $producto->descripcionP }} (Precio: {{ $producto->precioP }})
                            </option>
                        @endforeach
                    </select>
                </div>
        
                <!-- Agrupar el label y input de cantidad -->
                <div style="display: flex; align-items: center; gap: 5px;">
                    <label for="cantidad" class="col-form-label">Cantidad</label>
                    <input type="number" class="form-control cantidadInput" name="productos[0][cantidad]" value="0" min="1" required disabled>
                </div>
        
                <!-- Botón eliminar -->
                <div>
                    <button type="button" class="btn btn-danger removeProducto">Eliminar</button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-primary" id="addProducto">Agregar Producto</button>

        <hr>

        <!-- Campos de Venta -->
        <h4>Resumen de Venta</h4>
        <div class="form-group">
            <label for="subTotal">Subtotal (S/.)</label>
            <input type="number" class="form-control" id="subTotal" name="subTotal" readonly>
        </div>

        <div class="form-group">
            <label for="IGV">IGV (18%)</label>
            <input type="number" class="form-control" id="IGV" name="IGV" readonly>
        </div>

        <div class="form-group">
            <label for="montoTotal">Monto Total (S/.)</label>
            <input type="number" class="form-control" id="montoTotal" name="montoTotal" readonly>
        </div>

        <!-- Botón para Proceder con el Pago -->
        <button type="submit" class="btn btn-success">Proceder con el Pago</button>

    </form>

@stop
