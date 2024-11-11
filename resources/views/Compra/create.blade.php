@extends('adminlte::page')

@section('title', 'Registrar Compra')

@section('content_header')
    <h1>Registro de Compra</h1>
@stop

@section('content')
    <a href="{{ route('compras.index') }}" class="btn btn-secondary mb-3">Cancelar</a>

    <form action="{{ route('compras.store') }}" method="POST" id="formCompra">
        @csrf

        <!-- Selección del Proveedor -->
        <h4>Proveedor</h4>
        <div class="form-group">
            <select class="form-control" name="proveedor_id" required>
                <option value="">Seleccionar Proveedor</option>
                @foreach($proveedores as $proveedor)
                    <option value="{{ $proveedor->id }}">{{ $proveedor->nombreProveedor }} {{ $proveedor->apellidoProveedor }}</option>
                @endforeach
            </select>
        </div>

        <!-- Detalle de Compra -->
        <h4>Seleccionar Productos</h4>
        <div id="detalleCompra" class="container-fluid">
            <!-- Producto base (template para clonar) -->
            <div class="form-group row productoRow" id="productoTemplate" style="justify-content: center; align-items: center; gap:15px;">
                <!-- Agrupar el label y select del producto -->
                <div style="display: flex; align-items: center; gap: 5px;">
                    <label for="producto_id" class="col-form-label" id="productoLabel"></label>
                    <select class="form-control productoSelect" name="productos[0][id]" required>
                        <option value="">Seleccionar Producto</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}" data-precio="{{ $producto->precioP }}" data-stock="{{ $producto->stockP }}">
                                {{ $producto->descripcionP }} (Precio: S/{{ $producto->precioP }})
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

        <!-- Botón para registrar compra -->
        <button type="submit" class="btn btn-success">Registrar orden de compra</button>
    </form>
@stop

@section('js')
    <script>
        let productoIndex = 1;

        // Al cargar la página, asignar "Producto 1" al primer label
        document.getElementById('productoLabel').innerText = 'Producto ' + productoIndex;

        // Función para agregar un nuevo producto
        document.getElementById('addProducto').addEventListener('click', function () {
            productoIndex++; // Incrementa el número del producto

            const template = document.getElementById('productoTemplate').cloneNode(true);
            template.id = '';
            template.style.display = 'flex';  // Asegura que se disponga en fila
            template.style.alignItems = 'center';  // Centra verticalmente los elementos
            template.classList.add('productoRow');  // Asegura que mantenga la clase

            // Cambiar los nombres de los inputs dinámicamente para que sean únicos
            template.querySelectorAll('select, input').forEach(function (element) {
                const name = element.getAttribute('name');
                element.setAttribute('name', name.replace('[0]', '[' + (productoIndex - 1) + ']')); // Usamos productoIndex - 1 para evitar confusión
            });

            // Actualizar el label del nuevo producto
            const nuevoLabel = template.querySelector('label');
            nuevoLabel.innerText = 'Producto ' + productoIndex;

            // Inicializa el campo de cantidad a 0 y deshabilitado
            const cantidadInput = template.querySelector('.cantidadInput');
            cantidadInput.value = 0; // Restablece la cantidad a 0
            cantidadInput.disabled = true; // Deshabilita el campo de cantidad por defecto

            // Restablece el select de producto al valor por defecto
            const productoSelect = template.querySelector('.productoSelect');
            productoSelect.value = ""; // Resetea el select del producto

            // Añadir el nuevo producto al detalle de compra
            document.getElementById('detalleCompra').appendChild(template);
        });

        // Evento de cambio para habilitar la cantidad al seleccionar un producto
        document.getElementById('detalleCompra').addEventListener('change', function (event) {
            if (event.target.classList.contains('productoSelect')) {
                const cantidadInput = event.target.closest('.productoRow').querySelector('.cantidadInput');
                if (event.target.value !== "") {
                    // Habilita la cantidad y establece el valor inicial en 1
                    cantidadInput.disabled = false;
                    cantidadInput.value = 1;
                } else {
                    // Deshabilita y resetea la cantidad si no hay producto seleccionado
                    cantidadInput.disabled = true;
                    cantidadInput.value = 0;
                }
            }
        });

        // Eliminar producto
        document.getElementById('detalleCompra').addEventListener('click', function (event) {
            if (event.target.classList.contains('removeProducto')) {
                event.target.closest('.productoRow').remove();
            }
        });

    </script>
@stop
