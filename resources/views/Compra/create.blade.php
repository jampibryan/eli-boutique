@extends('adminlte::page')

@section('title', 'Registrar Compra')

@section('content_header')
@stop

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1" style="color: #2C2C2C; font-weight: 700;">
                    <i class="fas fa-shopping-cart" style="color: #28a745;"></i> Registrar Orden de Compra
                </h3>
                <p class="text-muted mb-0">Seleccione el proveedor y los productos a comprar</p>
            </div>
            <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card shadow-sm" style="border: none; border-top: 4px solid #28a745;">
        <div class="card-body p-4">
            <form action="{{ route('compras.store') }}" method="POST" id="formCompra">
                @csrf

                <div class="row g-3">
                    <div class="col-12 mb-2">
                        <h5 class="border-bottom pb-2 mb-0" style="color: #2C2C2C;">
                            <i class="fas fa-truck" style="color: #28a745;"></i> Información del Proveedor
                        </h5>
                    </div>

                    <div class="col-12">
                        <label for="proveedor_id" class="form-label fw-semibold">
                            <i class="fas fa-store text-muted"></i> Proveedor <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('proveedor_id') is-invalid @enderror" 
                                name="proveedor_id" id="proveedor_id" required>
                            <option value="">Seleccionar Proveedor</option>
                            @foreach($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}">
                                    {{ $proveedor->nombreEmpresa }} - {{ $proveedor->nombreProveedor }} {{ $proveedor->apellidoProveedor }}
                                </option>
                            @endforeach
                        </select>
                        @error('proveedor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> El tipo de comprobante se seleccionará al momento del pago
                        </small>
                    </div>

                    <div class="col-12 mt-3">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <h5 class="mb-0" style="color: #2C2C2C;">
                                <i class="fas fa-boxes" style="color: #28a745;"></i> Productos a Comprar
                            </h5>
                            <button type="button" class="btn btn-sm" style="background: #28a745; color: white;" id="addProducto">
                                <i class="fas fa-plus"></i> Agregar Producto
                            </button>
                        </div>
                    </div>

                    <div class="col-12">
                        <div id="detalleCompra" class="row g-2">
                            <div class="col-12 producto-row">
                                <div class="card border shadow-sm">
                                    <div class="card-body">
                                        <div class="row align-items-end g-3">
                                            <div class="col-md-1 text-center">
                                                <span class="badge bg-secondary producto-numero" style="font-size: 1rem; padding: 0.5rem;">1</span>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold mb-2">
                                                    <i class="fas fa-box text-muted"></i> Producto
                                                </label>
                                                <select class="form-select productoSelect" name="productos[0][id]" required>
                                                    <option value="">Seleccionar Producto</option>
                                                    @foreach($productos as $producto)
                                                        <option value="{{ $producto->id }}" 
                                                                data-tallas='@json($producto->tallaStocks->pluck("talla"))'>
                                                            {{ $producto->descripcionP }} - S/ {{ number_format($producto->precioP, 2) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold mb-2">
                                                    <i class="fas fa-ruler text-muted"></i> Talla
                                                </label>
                                                <select class="form-select tallaSelect" name="productos[0][talla_id]" required disabled>
                                                    <option value="">Seleccionar Talla</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-semibold mb-2">
                                                    <i class="fas fa-hashtag text-muted"></i> Cantidad
                                                </label>
                                                <input type="number" class="form-control cantidadInput" 
                                                       name="productos[0][cantidad]" value="1" min="1" required disabled>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <button type="button" class="btn btn-danger removeProducto w-100">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="alert alert-info border-0" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> El estado inicial de la orden será "Borrador". El tipo de comprobante (Boleta/Factura) se seleccionará al momento de realizar el pago.
                        </div>
                        <div class="d-flex gap-2 justify-content-end border-top pt-3">
                            <a href="{{ route('compras.index') }}" class="btn btn-light px-4">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn px-4" style="background: #28a745; color: white;">
                                <i class="fas fa-check"></i> Registrar Orden de Compra
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/boutique-cards.css') }}">
    
    <style>
        body {
            background: #f4f6f9;
        }
        
        .page-header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .form-label {
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.625rem 0.875rem;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
        }
        
        .producto-row .card {
            transition: all 0.3s;
        }
        
        .producto-row .card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let productoIndex = 1;

        document.addEventListener('DOMContentLoaded', function() {
            setupProductListeners(document.querySelector('.producto-row'));
        });

        function setupProductListeners(row) {
            const productoSelect = row.querySelector('.productoSelect');
            const tallaSelect = row.querySelector('.tallaSelect');
            const cantidadInput = row.querySelector('.cantidadInput');
            
            productoSelect.addEventListener('change', function() {
                tallaSelect.innerHTML = '<option value="">Seleccionar Talla</option>';
                cantidadInput.disabled = true;
                cantidadInput.value = 1;
                
                if (this.value !== "") {
                    const selectedOption = this.options[this.selectedIndex];
                    const tallas = JSON.parse(selectedOption.dataset.tallas || '[]');
                    
                    if (tallas.length > 0) {
                        tallas.forEach(talla => {
                            const option = document.createElement('option');
                            option.value = talla.id;
                            option.textContent = talla.descripcion;
                            tallaSelect.appendChild(option);
                        });
                        tallaSelect.disabled = false;
                    } else {
                        tallaSelect.innerHTML = '<option value="">Sin tallas disponibles</option>';
                        tallaSelect.disabled = true;
                    }
                } else {
                    tallaSelect.disabled = true;
                }
            });
            
            tallaSelect.addEventListener('change', function() {
                if (this.value !== "") {
                    cantidadInput.disabled = false;
                    cantidadInput.value = 1;
                } else {
                    cantidadInput.disabled = true;
                    cantidadInput.value = 1;
                }
            });
        }

        document.getElementById('addProducto').addEventListener('click', function () {
            const template = document.querySelector('.producto-row').cloneNode(true);
            const detalleCompra = document.getElementById('detalleCompra');
            
            const productoSelect = template.querySelector('.productoSelect');
            const tallaSelect = template.querySelector('.tallaSelect');
            const cantidadInput = template.querySelector('.cantidadInput');
            
            productoSelect.name = `productos[${productoIndex}][id]`;
            productoSelect.value = "";
            
            tallaSelect.name = `productos[${productoIndex}][talla_id]`;
            tallaSelect.innerHTML = '<option value="">Seleccionar Talla</option>';
            tallaSelect.disabled = true;
            
            cantidadInput.name = `productos[${productoIndex}][cantidad]`;
            cantidadInput.value = 1;
            cantidadInput.disabled = true;
            
            template.querySelector('.producto-numero').textContent = productoIndex + 1;
            
            setupProductListeners(template);
            
            detalleCompra.appendChild(template);
            productoIndex++;
            updateProductNumbers();
        });

        document.getElementById('detalleCompra').addEventListener('click', function (event) {
            if (event.target.classList.contains('removeProducto') || event.target.closest('.removeProducto')) {
                const rows = document.querySelectorAll('.producto-row');
                if (rows.length > 1) {
                    event.target.closest('.producto-row').remove();
                    updateProductNumbers();
                } else {
                    alert('Debe haber al menos un producto en la orden de compra.');
                }
            }
        });

        function updateProductNumbers() {
            document.querySelectorAll('.producto-row').forEach((row, index) => {
                row.querySelector('.producto-numero').textContent = index + 1;
            });
        }

        document.getElementById('formCompra').addEventListener('submit', function(e) {
            const rows = document.querySelectorAll('.producto-row');
            let valid = true;
            
            rows.forEach(row => {
                const producto = row.querySelector('.productoSelect').value;
                const talla = row.querySelector('.tallaSelect').value;
                const cantidad = row.querySelector('.cantidadInput').value;
                
                if (!producto || !talla || !cantidad) {
                    valid = false;
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Por favor complete todos los campos de productos, tallas y cantidades.');
            }
        });
    </script>
@stop
