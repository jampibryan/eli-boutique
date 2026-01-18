@extends('adminlte::page')

@section('title', 'Proveedores')

@section('content_header')
@stop

@section('content')
    <!-- Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1" style="color: #2C2C2C; font-weight: 700;">
                    <i class="fas fa-truck-loading" style="color: #28a745;"></i> Editar Proveedor
                </h3>
                <p class="text-muted mb-0">{{ $proveedor->nombreEmpresa }}</p>
            </div>
            <a href="{{ route('proveedores.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <div class="card shadow-sm" style="border: none; border-top: 4px solid #28a745;">
        <div class="card-body p-4">
            <form action="{{ route('proveedores.update', $proveedor->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- Información de la Empresa -->
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3" style="color: #2C2C2C;">
                            <i class="fas fa-building" style="color: #28a745;"></i> Información de la Empresa
                        </h5>
                    </div>

                    <!-- Empresa -->
                    <div class="col-md-6">
                        <label for="nombreEmpresa" class="form-label fw-semibold">
                            <i class="fas fa-store text-muted"></i> Nombre de la Empresa <span class="text-danger">*</span>
                        </label>
                        <input id="nombreEmpresa" name="nombreEmpresa" type="text" 
                               class="form-control @error('nombreEmpresa') is-invalid @enderror" 
                               value="{{ $proveedor->nombreEmpresa }}">
                        @error('nombreEmpresa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- RUC -->
                    <div class="col-md-6">
                        <label for="RUC" class="form-label fw-semibold">
                            <i class="fas fa-id-card-alt text-muted"></i> RUC <span class="text-danger">*</span>
                        </label>
                        <input id="RUC" name="RUC" type="text" maxlength="11"
                               class="form-control @error('RUC') is-invalid @enderror" 
                               value="{{ $proveedor->RUC }}">
                        @error('RUC')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dirección -->
                    <div class="col-md-12">
                        <label for="direccionProveedor" class="form-label fw-semibold">
                            <i class="fas fa-map-marker-alt text-muted"></i> Dirección <span class="text-danger">*</span>
                        </label>
                        <input id="direccionProveedor" name="direccionProveedor" type="text" 
                               class="form-control @error('direccionProveedor') is-invalid @enderror" 
                               value="{{ $proveedor->direccionProveedor }}">
                        @error('direccionProveedor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Información del Contacto -->
                    <div class="col-12 mt-4">
                        <h5 class="border-bottom pb-2 mb-3" style="color: #2C2C2C;">
                            <i class="fas fa-user" style="color: #28a745;"></i> Información del Contacto
                        </h5>
                    </div>

                    <!-- Nombre del Contacto -->
                    <div class="col-md-6">
                        <label for="nombreProveedor" class="form-label fw-semibold">
                            <i class="fas fa-signature text-muted"></i> Nombre <span class="text-danger">*</span>
                        </label>
                        <input id="nombreProveedor" name="nombreProveedor" type="text" 
                               class="form-control @error('nombreProveedor') is-invalid @enderror" 
                               value="{{ $proveedor->nombreProveedor }}">
                        @error('nombreProveedor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Apellidos del Contacto -->
                    <div class="col-md-6">
                        <label for="apellidoProveedor" class="form-label fw-semibold">
                            <i class="fas fa-signature text-muted"></i> Apellidos <span class="text-danger">*</span>
                        </label>
                        <input id="apellidoProveedor" name="apellidoProveedor" type="text" 
                               class="form-control @error('apellidoProveedor') is-invalid @enderror" 
                               value="{{ $proveedor->apellidoProveedor }}">
                        @error('apellidoProveedor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Información de Contacto -->
                    <div class="col-12 mt-4">
                        <h5 class="border-bottom pb-2 mb-3" style="color: #2C2C2C;">
                            <i class="fas fa-address-book" style="color: #28a745;"></i> Datos de Contacto
                        </h5>
                    </div>

                    <!-- Correo -->
                    <div class="col-md-6">
                        <label for="correoProveedor" class="form-label fw-semibold">
                            <i class="fas fa-envelope text-muted"></i> Correo Electrónico <span class="text-danger">*</span>
                        </label>
                        <input id="correoProveedor" name="correoProveedor" type="email" 
                               class="form-control @error('correoProveedor') is-invalid @enderror" 
                               value="{{ $proveedor->correoProveedor }}">
                        @error('correoProveedor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div class="col-md-6">
                        <label for="telefonoProveedor" class="form-label fw-semibold">
                            <i class="fas fa-phone text-muted"></i> Teléfono <span class="text-danger">*</span>
                        </label>
                        <input id="telefonoProveedor" name="telefonoProveedor" type="text" maxlength="9"
                               class="form-control @error('telefonoProveedor') is-invalid @enderror" 
                               value="{{ $proveedor->telefonoProveedor }}">
                        @error('telefonoProveedor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones de acción -->
                    <div class="col-12 mt-4">
                        <div class="d-flex gap-2 justify-content-end border-top pt-3">
                            <a href="{{ route('proveedores.index') }}" class="btn btn-light px-4">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn px-4" style="background: #28a745; color: white;">
                                <i class="fas fa-save"></i> Actualizar Proveedor
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
    </style>
@stop

@section('js')
    <script>
        // Validación de solo números para RUC y teléfono
        document.getElementById('RUC').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        document.getElementById('telefonoProveedor').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
@stop
