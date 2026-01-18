@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
@stop

@section('content')
    <!-- Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1" style="color: #2C2C2C; font-weight: 700;">
                    <i class="fas fa-user-plus" style="color: #D4AF37;"></i> Registrar Cliente
                </h3>
                <p class="text-muted mb-0">Complete los datos del nuevo cliente</p>
            </div>
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <div class="card shadow-sm" style="border: none; border-top: 4px solid #D4AF37;">
        <div class="card-body p-4">
            <form action="{{ route('clientes.store') }}" method="POST" id="clienteForm">
                @csrf
                @if(isset($redirect))
                    <input type="hidden" name="redirect" value="{{ $redirect }}">
                @endif

                <div class="row g-4">
                    <!-- Información Personal -->
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3" style="color: #2C2C2C;">
                            <i class="fas fa-user" style="color: #D4AF37;"></i> Información Personal
                        </h5>
                    </div>

                    <!-- Nombre -->
                    <div class="col-md-6">
                        <label for="nombreCliente" class="form-label fw-semibold">
                            <i class="fas fa-signature text-muted"></i> Nombre <span class="text-danger">*</span>
                        </label>
                        <input id="nombreCliente" name="nombreCliente" type="text" 
                               class="form-control @error('nombreCliente') is-invalid @enderror" 
                               value="{{ old('nombreCliente') }}" placeholder="Ingrese el nombre">
                        @error('nombreCliente')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Apellidos -->
                    <div class="col-md-6">
                        <label for="apellidoCliente" class="form-label fw-semibold">
                            <i class="fas fa-signature text-muted"></i> Apellidos <span class="text-danger">*</span>
                        </label>
                        <input id="apellidoCliente" name="apellidoCliente" type="text" 
                               class="form-control @error('apellidoCliente') is-invalid @enderror" 
                               value="{{ old('apellidoCliente') }}" placeholder="Ingrese los apellidos">
                        @error('apellidoCliente')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Género -->
                    <div class="col-md-6">
                        <label for="tipo_genero_id" class="form-label fw-semibold">
                            <i class="fas fa-venus-mars text-muted"></i> Género <span class="text-danger">*</span>
                        </label>
                        <select id="tipo_genero_id" name="tipo_genero_id" 
                                class="form-select @error('tipo_genero_id') is-invalid @enderror">
                            <option value="">Seleccionar género</option>
                            @foreach($generos as $genero)
                                <option value="{{ $genero->id }}" {{ old('tipo_genero_id') == $genero->id ? 'selected' : '' }}>
                                    {{ $genero->descripcionTG }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_genero_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- DNI -->
                    <div class="col-md-6">
                        <label for="dniCliente" class="form-label fw-semibold">
                            <i class="fas fa-id-card text-muted"></i> DNI <span class="text-danger">*</span>
                        </label>
                        <input id="dniCliente" name="dniCliente" type="text" maxlength="8"
                               class="form-control @error('dniCliente') is-invalid @enderror" 
                               value="{{ old('dniCliente') }}" placeholder="12345678">
                        @error('dniCliente')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Información de Contacto -->
                    <div class="col-12 mt-4">
                        <h5 class="border-bottom pb-2 mb-3" style="color: #2C2C2C;">
                            <i class="fas fa-address-book" style="color: #D4AF37;"></i> Información de Contacto
                        </h5>
                    </div>

                    <!-- Correo -->
                    <div class="col-md-6">
                        <label for="correoCliente" class="form-label fw-semibold">
                            <i class="fas fa-envelope text-muted"></i> Correo Electrónico <span class="text-danger">*</span>
                        </label>
                        <input id="correoCliente" name="correoCliente" type="email" 
                               class="form-control @error('correoCliente') is-invalid @enderror" 
                               value="{{ old('correoCliente') }}" placeholder="ejemplo@correo.com">
                        @error('correoCliente')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div class="col-md-6">
                        <label for="telefonoCliente" class="form-label fw-semibold">
                            <i class="fas fa-phone text-muted"></i> Teléfono <span class="text-danger">*</span>
                        </label>
                        <input id="telefonoCliente" name="telefonoCliente" type="text" maxlength="9"
                               class="form-control @error('telefonoCliente') is-invalid @enderror" 
                               value="{{ old('telefonoCliente') }}" placeholder="987654321">
                        @error('telefonoCliente')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones de acción -->
                    <div class="col-12 mt-4">
                        <div class="d-flex gap-2 justify-content-end border-top pt-3">
                            <a href="{{ route('clientes.index') }}" class="btn btn-light px-4">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-boutique-gold px-4">
                                <i class="fas fa-save"></i> Registrar Cliente
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
            border-color: #D4AF37;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.15);
        }
    </style>
@stop

@section('js')
    <script>
        // Validación de solo números para DNI y teléfono
        document.getElementById('dniCliente').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        document.getElementById('telefonoCliente').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
@stop
