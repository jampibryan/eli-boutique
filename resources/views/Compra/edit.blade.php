@extends('adminlte::page')

@section('title', 'Editar Compra')

@section('content_header')
    <h1>Editar Compra</h1>
@stop

@section('content')
    <a href="{{ route('compras.index') }}" class="btn btn-secondary mb-3">Cancelar</a>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('compras.update', $compra->id) }}" method="POST" id="formCompra">
        @csrf
        @method('PUT')

        <!-- Selección del Proveedor -->
        <h4>Proveedor</h4>
        <div class="form-group">
            <select class="form-control" name="proveedor_id" required>
                <option value="">Seleccionar Proveedor</option>
                @foreach($proveedores as $proveedor)
                    <option value="{{ $proveedor->id }}" {{ $compra->proveedor_id == $proveedor->id ? 'selected' : '' }}>
                        {{ $proveedor->nombreProveedor }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Detalle de Compra -->
        <h4>Seleccionar Productos</h4>
        <div id="detalleCompra" class="container-fluid">
            @foreach($compra->detalles as $index => $detalle)
            <div class="form-group row productoRow" style="justify-content: center; align-items: center; gap: 15px;">
                <!-- Seleccionar Producto -->
                <div style="display: flex; align-items: center; gap: 5px;">
                    <label for="producto_id" class="col-form-label">Producto {{ $index + 1 }}</label>
                    <select class="form-control productoSelect" name="productos[{{ $index }}][id]" required>
                        <option value="">Seleccionar Producto</option>
                        @foreach($productos as $prod)
                            <option value="{{ $prod->id }}" {{ $detalle->producto_id == $prod->id ? 'selected' : '' }} 
                                    data-precio="{{ $prod->precioP }}" data-stock="{{ $prod->stockP }}">
                                {{ $prod->descripcionP }} (Precio: S/ {{ $prod->precioP }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Cantidad -->
                <div style="display: flex; align-items: center; gap: 5px;">
                    <label for="cantidad" class="col-form-label">Cantidad</label>
                    <input type="number" class="form-control cantidadInput" name="productos[{{ $index }}][cantidad]" value="{{ $detalle->cantidad }}" min="1" required>
                </div>

                <!-- Botón eliminar -->
                <div>
                    <button type="button" class="btn btn-danger removeProducto">Eliminar</button>
                </div>
            </div>
            @endforeach
        </div>

        <button type="button" class="btn btn-primary" id="addProducto">Agregar Producto</button>

        <hr>

        <!-- Botón para registrar compra -->
        <button type="submit" class="btn btn-success">Actualizar compra</button>

    </form>

@stop
@section('js')
    <script>
        let productoIndex = {{ count($compra->detalles) }};

        document.getElementById('addProducto').addEventListener('click', function () {
            productoIndex++; // Incrementa el número del producto

            const template = document.createElement('div');
            template.className = 'form-group row productoRow';
            template.style.justifyContent = 'center';
            template.style.alignItems = 'center';
            template.style.gap = '15px';

            // Añadir HTML del nuevo producto
            template.innerHTML = `
                <div class="form-group row productoRow" style="justify-content: center; align-items: center; gap: 15px;">
                    <!-- Seleccionar Producto -->
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <label for="producto_id" class="col-form-label">Producto ${productoIndex}</label>
                        <select class="form-control productoSelect" name="productos[${productoIndex - 1}][id]" required>
                            <option value="">Seleccionar Producto</option>
                            @foreach($productos as $prod)
                                <option value="{{ $prod->id }}" data-precio="{{ $prod->precioP }}" data-stock="{{ $prod->stockP }}">
                                    {{ $prod->descripcionP }} (Precio: S/ {{ $prod->precioP }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Cantidad -->
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <label for="cantidad" class="col-form-label">Cantidad</label>
                        <input type="number" class="form-control cantidadInput" name="productos[${productoIndex - 1}][cantidad]" min="1" required>
                    </div>

                    <!-- Botón eliminar -->
                    <div>
                        <button type="button" class="btn btn-danger removeProducto">Eliminar</button>
                    </div>
                </div>
            `;

            // Añadir el nuevo producto al detalle de compra
            document.getElementById('detalleCompra').appendChild(template);

            // Agregar evento change al select para que cambie la cantidad a 1 al seleccionar un producto
            const selectElement = template.querySelector('.productoSelect');
            const cantidadInput = template.querySelector('.cantidadInput');

            selectElement.addEventListener('change', function () {
                if (this.value) {
                    cantidadInput.value = 1; // Establecer cantidad a 1
                } else {
                    cantidadInput.value = ''; // Limpiar la cantidad si no hay producto seleccionado
                }
            });
        });

        // Eliminar producto
        document.getElementById('detalleCompra').addEventListener('click', function (event) {
            if (event.target.classList.contains('removeProducto')) {
                event.target.closest('.productoRow').remove();
                calcularTotales();
            }
        });

    </script>
@stop
