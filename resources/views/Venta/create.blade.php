@extends('adminlte::page')

@section('title', 'Registrar Venta')

@section('content_header')
    <h1>Registro de Venta</h1>
@stop

@section('content')

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

@section('js')
    <script>
        let productoIndex = 1;
        
        // Al cargar la página, asignar "Producto 1" al primer label
        document.getElementById('productoLabel').innerText = 'Producto ' + productoIndex;

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

            // Habilitar el campo cantidad al seleccionar un producto
            const productoSelect = template.querySelector('.productoSelect');
            const cantidadInput = template.querySelector('.cantidadInput');
            productoSelect.addEventListener('change', function () {
                if (productoSelect.value) {
                    cantidadInput.disabled = false;  // Habilita el campo de cantidad
                } else {
                    cantidadInput.disabled = true;   // Deshabilita si no se ha seleccionado un producto
                }
            });

            // Añadir el nuevo producto al detalle de venta
            document.getElementById('detalleVenta').appendChild(template);
        });

        // Habilitar la cantidad solo si se selecciona un producto
        document.getElementById('detalleVenta').addEventListener('change', function (event) {
            if (event.target.classList.contains('productoSelect')) {
                const cantidadInput = event.target.closest('.productoRow').querySelector('.cantidadInput');
                if (event.target.value !== "") {
                    // Habilitar y poner la cantidad en 1 si se selecciona un producto
                    cantidadInput.disabled = false;
                    cantidadInput.value = 1;
                } else {
                    // Si no hay producto seleccionado, desactivar y resetear la cantidad
                    cantidadInput.disabled = true;
                    cantidadInput.value = 0;
                }
                calcularTotales();
            }
        });

        // Eliminar producto
        document.getElementById('detalleVenta').addEventListener('click', function (event) {
            if (event.target.classList.contains('removeProducto')) {
                event.target.closest('.row').remove();
                calcularTotales();
            }
        });

        // Actualizar totales al cambiar producto o cantidad
        document.getElementById('detalleVenta').addEventListener('change', function (event) {
            if (event.target.classList.contains('productoSelect') || event.target.classList.contains('cantidadInput')) {
                calcularTotales();
            }
        });

        // Función para calcular los totales
        function calcularTotales() {
            let subtotal = 0;

            document.querySelectorAll('.productoSelect').forEach(function (selectElement, index) {
                const cantidad = document.querySelectorAll('.cantidadInput')[index].value;
                const precio = selectElement.options[selectElement.selectedIndex].getAttribute('data-precio');

                subtotal += cantidad * precio;
            });

            const igv = subtotal * 0.18;
            const montoTotal = subtotal + igv;

            // Mostrar los resultados en los campos correspondientes
            document.getElementById('subTotal').value = subtotal.toFixed(2);
            document.getElementById('IGV').value = igv.toFixed(2);
            document.getElementById('montoTotal').value = montoTotal.toFixed(2);
        }

        // Calcula los totales al cargar la página por si ya hay productos seleccionados
        calcularTotales();
    </script>
@stop
