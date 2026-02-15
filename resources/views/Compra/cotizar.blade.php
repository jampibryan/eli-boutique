@extends('adminlte::page')

@section('title', 'Cotizar Orden de Compra')

@section('content_header')
@stop

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1" style="color: #2C2C2C; font-weight: 700;">
                    <i class="fas fa-file-invoice-dollar" style="color: #28a745;"></i> Cotizar Orden de Compra
                </h3>
                <p class="text-muted mb-0">Ingrese los precios cotizados por el proveedor</p>
            </div>
            <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información del Proveedor -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm" style="border: none; border-top: 4px solid #28a745;">
                <div class="card-body p-4">
                    <h5 class="border-bottom pb-2 mb-3" style="color: #2C2C2C;">
                        <i class="fas fa-truck" style="color: #28a745;"></i> Información del Proveedor
                    </h5>
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-2"><strong>Empresa:</strong> {{ $compra->proveedor->nombreEmpresa }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-2"><strong>Contacto:</strong> {{ $compra->proveedor->nombreProveedor }} {{ $compra->proveedor->apellidoProveedor }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-2"><strong>Código Orden:</strong> {{ $compra->codigoCompra }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Cotización -->
        <div class="col-12">
            <form action="{{ route('compras.guardar-cotizacion', $compra->id) }}" method="POST" id="formCotizacion" enctype="multipart/form-data">
                @csrf

                <div class="card shadow-sm" style="border: none; border-top: 4px solid #28a745;">
                    <div class="card-body p-4">
                        <h5 class="border-bottom pb-2 mb-3" style="color: #2C2C2C;">
                            <i class="fas fa-list-alt" style="color: #28a745;"></i> Productos Solicitados
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white;">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 35%;">Producto</th>
                                        <th style="width: 10%;" class="text-center">Talla</th>
                                        <th style="width: 10%;" class="text-center">Cantidad</th>
                                        <th style="width: 20%;" class="text-center">Precio Unitario <span class="text-warning">*</span></th>
                                        <th style="width: 20%;" class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($compra->detalles as $index => $detalle)
                                        <tr>
                                            <td><strong>{{ $index + 1 }}</strong></td>
                                            <td>
                                                <div>
                                                    <strong style="color: #2C2C2C;">{{ $detalle->producto->descripcionP }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-barcode"></i> Código: {{ $detalle->producto->codigoP }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge" style="background: #6c757d; color: white; font-size: 0.9rem; padding: 0.4rem 0.8rem;">
                                                    {{ $detalle->talla->descripcion }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span style="font-size: 1.1rem; font-weight: 600; color: #28a745;">
                                                    {{ $detalle->cantidad }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="input-group" style="max-width: 180px; margin: 0 auto;">
                                                    <span class="input-group-text" style="background: #28a745; color: white; border: none;">
                                                        <i class="fas fa-dollar-sign"></i>
                                                    </span>
                                                    <input type="number" 
                                                           class="form-control form-control-lg text-end precio-cotizado" 
                                                           name="precio_cotizado[{{ $detalle->id }}]" 
                                                           step="0.01" 
                                                           min="0"
                                                           value=""
                                                           placeholder="0.00"
                                                           data-cantidad="{{ $detalle->cantidad }}"
                                                           data-detalle-id="{{ $detalle->id }}"
                                                           style="font-weight: 600; border: 2px solid #28a745;"
                                                           required>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <strong class="subtotal-linea" id="subtotal-{{ $detalle->id }}" style="font-size: 1.1rem; color: #28a745;">
                                                    S/. 0.00
                                                </strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot style="background: #f8f9fa; border-top: 2px solid #28a745;">
                                    <tr>
                                        <td colspan="5" class="text-end py-3"><strong style="font-size: 1.1rem;">Subtotal General:</strong></td>
                                        <td class="text-end py-3"><strong id="total-subtotal" style="font-size: 1.2rem; color: #28a745;">S/. 0.00</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Información Adicional y Totales -->
                <div class="row mt-4">
                    <!-- Columna Izquierda: Información Adicional -->
                    <div class="col-md-7">
                        <div class="card shadow-sm" style="border: none; border-top: 4px solid #17a2b8; height: 100%;">
                            <div class="card-body p-4">
                                <h5 class="border-bottom pb-2 mb-3" style="color: #2C2C2C;">
                                    <i class="fas fa-info-circle" style="color: #17a2b8;"></i> Información Adicional
                                </h5>

                                <div class="mb-3">
                                    <label for="pdf_cotizacion" class="form-label fw-semibold">
                                        <i class="fas fa-file-pdf text-danger"></i> PDF de Cotización del Proveedor <span class="text-muted">(opcional)</span>
                                    </label>
                                    <input type="file" 
                                           class="form-control form-control-lg" 
                                           id="pdf_cotizacion" 
                                           name="pdf_cotizacion"
                                           accept=".pdf"
                                           style="border: 2px dashed #17a2b8;">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> Suba el PDF enviado por el proveedor (máx. 10MB)
                                    </small>
                                </div>

                                <div class="mb-3">
                                    <label for="descuento" class="form-label fw-semibold">
                                        <i class="fas fa-percent text-warning"></i> Descuento General (S/.)
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text" style="background: #ffc107; color: white; border: none;">
                                            <i class="fas fa-tag"></i>
                                        </span>
                                        <input type="number" 
                                               class="form-control form-control-lg" 
                                               id="descuento" 
                                               name="descuento"
                                               step="0.01"
                                               min="0"
                                               value="0"
                                               style="border: 2px solid #ffc107; font-weight: 600;"
                                               placeholder="0.00">
                                    </div>
                                </div>

                                <div>
                                    <label for="notas_proveedor" class="form-label fw-semibold">
                                        <i class="fas fa-sticky-note text-info"></i> Notas / Observaciones
                                    </label>
                                    <textarea class="form-control" 
                                              id="notas_proveedor" 
                                              name="notas_proveedor" 
                                              rows="3"
                                              style="border: 2px solid #e9ecef;"
                                              placeholder="Tiempo de entrega, condiciones especiales, garantías, etc."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Resumen de Totales -->
                    <div class="col-md-5">
                        <div class="card shadow-sm" style="border: none; border-top: 4px solid #28a745;">
                            <div class="card-body p-4">
                                <h5 class="border-bottom pb-2 mb-4" style="color: #2C2C2C;">
                                    <i class="fas fa-calculator" style="color: #28a745;"></i> Resumen de Totales
                                </h5>
                                
                                <div class="mb-3 p-3" style="background: #f8f9fa; border-radius: 8px;">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span style="color: #6c757d;">Subtotal:</span>
                                        <strong id="display-subtotal" style="font-size: 1.1rem;">S/. 0.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span style="color: #6c757d;">Descuento:</span>
                                        <strong id="display-descuento" style="color: #dc3545; font-size: 1.1rem;">- S/. 0.00</strong>
                                    </div>
                                    <hr style="margin: 0.8rem 0; border-top: 1px dashed #dee2e6;">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span style="color: #6c757d;">Base Imponible:</span>
                                        <strong id="display-base" style="font-size: 1.1rem;">S/. 0.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span style="color: #6c757d;">IGV (18%):</span>
                                        <strong id="display-igv" style="font-size: 1.1rem;">S/. 0.00</strong>
                                    </div>
                                    <hr style="margin: 0.8rem 0; border-top: 2px solid #28a745;">
                                    <div class="d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 1rem; border-radius: 6px; margin: -0.5rem; margin-top: 0.5rem;">
                                        <span style="color: white; font-size: 1.2rem; font-weight: 600;">TOTAL A PAGAR:</span>
                                        <strong id="display-total" style="color: white; font-size: 1.5rem;">S/. 0.00</strong>
                                    </div>
                                </div>

                                <div class="alert alert-info mb-0" style="border-left: 4px solid #17a2b8;">
                                    <small>
                                        <i class="fas fa-handshake"></i> <strong>Condición de pago:</strong><br>
                                        Pago contra entrega
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="d-flex justify-content-between align-items-center mt-4 p-3" style="background: #f8f9fa; border-radius: 8px;">
                    <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success btn-lg px-5" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none;">
                        <i class="fas fa-check-circle"></i> Guardar Cotización
                    </button>
                </div>

            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/boutique-cards.css') }}">
    <style>
        .input-group-text {
            font-weight: 600;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .precio-cotizado:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
        }
        .form-control-lg {
            font-size: 1rem;
        }
    </style>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Calcular totales iniciales
            calcularTotales();

            // Escuchar cambios en precios cotizados
            document.querySelectorAll('.precio-cotizado').forEach(input => {
                input.addEventListener('input', function() {
                    actualizarSubtotalLinea(this);
                    calcularTotales();
                });
            });

            // Escuchar cambio en descuento
            document.getElementById('descuento').addEventListener('input', calcularTotales);

            function actualizarSubtotalLinea(input) {
                const precio = parseFloat(input.value) || 0;
                const cantidad = parseFloat(input.dataset.cantidad);
                const detalleId = input.dataset.detalleId;
                const subtotal = precio * cantidad;
                
                document.getElementById('subtotal-' + detalleId).textContent = 
                    'S/. ' + subtotal.toFixed(2);
            }

            function calcularTotales() {
                let subtotal = 0;

                // Sumar todos los subtotales
                document.querySelectorAll('.precio-cotizado').forEach(input => {
                    const precio = parseFloat(input.value) || 0;
                    const cantidad = parseFloat(input.dataset.cantidad);
                    subtotal += precio * cantidad;
                });

                const descuento = parseFloat(document.getElementById('descuento').value) || 0;
                const baseImponible = subtotal - descuento;
                const igv = baseImponible * 0.18;
                const total = baseImponible + igv;

                // Actualizar displays
                document.getElementById('total-subtotal').textContent = 'S/. ' + subtotal.toFixed(2);
                document.getElementById('display-subtotal').textContent = 'S/. ' + subtotal.toFixed(2);
                document.getElementById('display-descuento').textContent = '- S/. ' + descuento.toFixed(2);
                document.getElementById('display-base').textContent = 'S/. ' + baseImponible.toFixed(2);
                document.getElementById('display-igv').textContent = 'S/. ' + igv.toFixed(2);
                document.getElementById('display-total').textContent = 'S/. ' + total.toFixed(2);
            }

            // Validación del formulario
            document.getElementById('formCotizacion').addEventListener('submit', function(e) {
                const preciosCotizados = document.querySelectorAll('.precio-cotizado');
                let valid = true;

                preciosCotizados.forEach(input => {
                    if (!input.value || parseFloat(input.value) < 0) {
                        valid = false;
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    alert('Por favor, ingrese todos los precios cotizados.');
                }
            });
        });
    </script>
@stop
