@extends('adminlte::page')

@section('title', 'Editar Venta')

@section('content_header')
    <h1>Editar Venta</h1>
@stop

@section('content')
    <a href="{{ route('ventas.index') }}" class="btn btn-secondary mb-3">Cancelar</a>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ventas.update', $venta->id) }}" method="POST" id="formVenta">
        @csrf
        @method('PUT')

        <!-- Campo de Codigo de Venta (oculto) -->
        <input type="hidden" name="codigoVenta" value="{{ $venta->codigoVenta }}">

        <!-- Selección del Cliente -->
        <h4>Comprador</h4>
        <div class="form-group">
            <select class="form-control" name="cliente_id" required>
                <option value="">Seleccionar Cliente</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ $venta->cliente_id == $cliente->id ? 'selected' : '' }}>
                        {{ $cliente->nombreCliente }} {{ $cliente->apellidoCliente }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Detalle de Venta -->
        <h4>Seleccionar Productos</h4>
        <div id="detalleVenta" class="container-fluid">
            @foreach($venta->detalles as $index => $detalle)
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

                <!-- Mostrar el Stock Inicial (antes de la venta) -->
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label for="stock_inicial" class="col-form-label">Stock Inicial</label>
                    <input type="number" class="form-control" name="productos[{{ $index }}][stock_inicial]" 
                        value="{{ $detalle->stock_inicial }}" readonly>
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

        <!-- Campos de Venta -->
        <h4>Resumen de Venta</h4>
        <div class="form-group">
            <label for="subTotal">Subtotal (S/.)</label>
            <input type="number" class="form-control" id="subTotal" name="subTotal" value="{{ $venta->subTotal }}" readonly>
        </div>

        <div class="form-group">
            <label for="IGV">IGV (18%)</label>
            <input type="number" class="form-control" id="IGV" name="IGV" value="{{ $venta->IGV }}" readonly>
        </div>

        <div class="form-group">
            <label for="montoTotal">Monto Total (S/.)</label>
            <input type="number" class="form-control" id="montoTotal" name="montoTotal" value="{{ $venta->montoTotal }}" readonly>
        </div>

        <!-- Botón para registrar venta -->
        <button type="submit" class="btn btn-success">Actualizar venta</button>

    </form>

@stop
@section('js')
    <script>
        let productoIndex = {{ count($venta->detalles) }};

        document.getElementById('addProducto').addEventListener('click', function () {
            productoIndex++; // Incrementa el número del producto

            const template = document.createElement('div');
            template.className = 'form-group row productoRow';
            template.style.justifyContent = 'center';
            template.style.alignItems = 'center';
            template.style.gap = '15px';

            // Añadir HTML del nuevo producto
            template.innerHTML = `
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
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label for="stock_inicial" class="col-form-label">Stock Inicial</label>
                    <input type="number" class="form-control" name="productos[${productoIndex - 1}][stock_inicial]" readonly>
                </div>
                <div style="display: flex; align-items: center; gap: 5px;">
                    <label for="cantidad" class="col-form-label">Cantidad</label>
                    <input type="number" class="form-control cantidadInput" name="productos[${productoIndex - 1}][cantidad]" min="1" required>
                </div>
                <div>
                    <button type="button" class="btn btn-danger removeProducto">Eliminar</button>
                </div>
            `;

            // Añadir el nuevo producto al detalle de venta
            document.getElementById('detalleVenta').appendChild(template);

           // Añadir evento para actualizar el stock inicial al seleccionar un producto
            const selectElement = template.querySelector('.productoSelect');
            selectElement.addEventListener('change', function () {
                const stockInicial = selectElement.options[selectElement.selectedIndex].getAttribute('data-stock');
                
                // Comprobar si el valor seleccionado es vacío
                if (selectElement.value === "") {
                    // Vaciar stock inicial y cantidad si no se selecciona un producto
                    template.querySelector('input[name="productos[' + (productoIndex - 1) + '][stock_inicial]"]').value = '';
                    template.querySelector('.cantidadInput').value = '';
                } else {
                    // Asignar el stock inicial y establecer la cantidad a 1
                    template.querySelector('input[name="productos[' + (productoIndex - 1) + '][stock_inicial]"]').value = stockInicial || 0;
                    template.querySelector('.cantidadInput').value = 1;
                }

                calcularTotales();
            });

        });

        // Eliminar producto
        document.getElementById('detalleVenta').addEventListener('click', function (event) {
            if (event.target.classList.contains('removeProducto')) {
                event.target.closest('.productoRow').remove();
                calcularTotales();
            }
        });

        // Actualizar totales al cambiar producto o cantidad
        document.getElementById('detalleVenta').addEventListener('change', function (event) {
            if (event.target.classList.contains('cantidadInput')) {
                const row = event.target.closest('.productoRow');
                const index = [...row.parentNode.children].indexOf(row); // Obtener el índice de la fila actual
                
                // Obtener el stock inicial del input de stock inicial
                const stockInicial = parseInt(row.querySelector('input[name="productos[' + index + '][stock_inicial]"]').value);
                let cantidad = parseInt(event.target.value);

                // Validar si la cantidad excede el stock inicial
                if (cantidad > stockInicial) {
                    alert('La cantidad no puede superar el stock inicial (' + stockInicial + ').');
                    row.querySelector('.cantidadInput').value = stockInicial;  // Ajustar al stock inicial
                    cantidad = stockInicial; // Asegurarse de que cantidad no exceda el stock inicial
                } else if (cantidad < 1) {
                    row.querySelector('.cantidadInput').value = 1; // Asegurarse de que la cantidad no sea menor a 1
                }

                calcularTotales();
            }
        });

        // Validar la cantidad mientras se escribe
        document.getElementById('detalleVenta').addEventListener('input', function (event) {
            if (event.target.classList.contains('cantidadInput')) {
                const row = event.target.closest('.productoRow');
                const index = [...row.parentNode.children].indexOf(row);
                const stockInicial = parseInt(row.querySelector('input[name="productos[' + index + '][stock_inicial]"]').value);
                
                // Asegurarse de que la cantidad no supere el stock inicial
                if (parseInt(event.target.value) > stockInicial) {
                    event.target.value = stockInicial; // Ajustar al stock inicial
                } else if (parseInt(event.target.value) < 1) {
                    event.target.value = 1; // Ajustar a 1 si es menor que 1
                }
            }
        });

        // Función para calcular los totales
        function calcularTotales() {
            let subtotal = 0;

            document.querySelectorAll('.productoRow').forEach(function (row) {
                const cantidad = row.querySelector('.cantidadInput').value || 0;
                const selectElement = row.querySelector('.productoSelect');
                const precio = selectElement.options[selectElement.selectedIndex].getAttribute('data-precio') || 0;

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
