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
            <div class="form-group row productoRow" id="productoTemplate" style="justify-content: center; align-items: center; gap:15px;">
                <!-- Agrupar el label y select del producto -->
                <div style="display: flex; align-items: center; gap: 5px;">
                    <label for="producto_id" class="col-form-label" id="productoLabel"></label>
                    <select class="form-control productoSelect" name="productos[0][id]" required>
                        <option value="">Seleccionar Producto</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}" data-precio="{{ $producto->precioP }}" data-stock="{{ $producto->stockP }}">
                                {{ $producto->descripcionP }} (Precio: S/{{ $producto->precioP }})
                                {{-- , Stock: {{ $producto->stockP }}) --}}
                            </option>
                        @endforeach
                    </select>
                </div>
        
                <!-- Mostrar stock inicial -->
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label for="stockP" class="col-form-label">Stock Actual</label>
                    <input type="number" class="form-control stockInput" name="productos[0][stockP]" value="0" readonly>
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

        <!-- Botón para registrar venta -->
        <button type="submit" class="btn btn-success">Registrar venta</button>
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
            const stockInput = template.querySelector('.stockInput');

            // Inicializa cantidad a 0 al agregar un nuevo producto
            cantidadInput.value = 0; // Establece el valor inicial de cantidad a 0
            cantidadInput.disabled = true; // Deshabilita el campo de cantidad por defecto
            stockInput.value = 0; // Establece el stock a 0 al principio

            productoSelect.addEventListener('change', function () {
                if (productoSelect.value) {
                    cantidadInput.disabled = false;  // Habilita el campo de cantidad
                    cantidadInput.value = 0; // Establece el valor inicial de cantidad a 0
                    stockInput.value = productoSelect.options[productoSelect.selectedIndex].getAttribute('data-stock');
                } else {
                    cantidadInput.disabled = true;   // Deshabilita si no se ha seleccionado un producto
                    cantidadInput.value = 0; // Resetea la cantidad a 0
                    stockInput.value = 0; // Resetea el stock a 0
                }
            });

            // Añadir el nuevo producto al detalle de venta
            document.getElementById('detalleVenta').appendChild(template);
        });

        // Habilitar la cantidad solo si se selecciona un producto
        document.getElementById('detalleVenta').addEventListener('change', function (event) {
            if (event.target.classList.contains('productoSelect')) {
                const cantidadInput = event.target.closest('.productoRow').querySelector('.cantidadInput');
                const stockInput = event.target.closest('.productoRow').querySelector('.stockInput');
                if (event.target.value !== "") {
                    // Habilitar y poner la cantidad en 1 si se selecciona un producto
                    cantidadInput.disabled = false;
                    cantidadInput.value = 1;
                    stockInput.value = event.target.options[event.target.selectedIndex].getAttribute('data-stock'); // Muestra el stock inicial
                } else {
                    // Si no hay producto seleccionado, desactivar y resetear la cantidad
                    cantidadInput.disabled = true;
                    cantidadInput.value = 0;
                    stockInput.value = 0; // Resetea el stock a 0
                }
                calcularTotales();
            }
        });

        // Validar la cantidad mientras se escribe
        document.getElementById('detalleVenta').addEventListener('input', function (event) {
            if (event.target.classList.contains('cantidadInput')) {
                const row = event.target.closest('.productoRow');
                
                // Obtener el stock actual de la fila correspondiente
                const stockActual = parseInt(row.querySelector('.stockInput').value);
                
                // Validar la cantidad ingresada
                if (parseInt(event.target.value) > stockActual) {
                    event.target.value = stockActual; // Ajustar al stock actual si excede
                } else if (parseInt(event.target.value) < 1) {
                    event.target.value = 1; // Ajustar a 1 si es menor que 1
                }
                
                // Volver a calcular los totales
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

            document.querySelectorAll('.productoRow').forEach(function (row) {
                const selectElement = row.querySelector('.productoSelect');
                const cantidadInput = row.querySelector('.cantidadInput');
                const stockInput = row.querySelector('.stockInput');

                const cantidad = parseInt(cantidadInput.value) || 0; // Asegúrate de convertir a número
                const precio = parseFloat(selectElement.options[selectElement.selectedIndex].getAttribute('data-precio')) || 0;

                // Verifica que la cantidad no supere el stock
                if (cantidad > parseInt(stockInput.value)) {
                    alert('La cantidad seleccionada supera el stock disponible.');
                    cantidadInput.value = stockInput.value; // Ajustar la cantidad a la máxima disponible
                } else {
                    subtotal += cantidad * precio;
                }
            });

            const igv = subtotal * 0.18;
            const montoTotal = subtotal + igv;

            // Mostrar los resultados en los campos correspondientes
            document.getElementById('subTotal').value = subtotal.toFixed(2);
            document.getElementById('IGV').value = igv.toFixed(2);
            document.getElementById('montoTotal').value = montoTotal.toFixed(2);
        }
    </script>
@stop
