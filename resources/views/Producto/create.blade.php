@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
@stop

@section('content')
    <!-- Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1" style="color: #2C2C2C; font-weight: 700;">
                    <i class="fas fa-box" style="color: #D4AF37;"></i> Registrar Producto
                </h3>
                <p class="text-muted mb-0">Complete los datos del nuevo producto</p>
            </div>
            <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <div class="card shadow-sm" style="border: none; border-top: 4px solid #D4AF37;">
        <div class="card-body p-4">
            <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-4">
                    <!-- Información Básica -->
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3" style="color: #2C2C2C;">
                            <i class="fas fa-info-circle" style="color: #D4AF37;"></i> Información Básica
                        </h5>
                    </div>

                    <!-- Código -->
                    <div class="col-md-4">
                        <label for="codigoP" class="form-label fw-semibold">
                            <i class="fas fa-barcode text-muted"></i> Código <span class="text-danger">*</span>
                        </label>
                        <input id="codigoP" name="codigoP" type="text" 
                               class="form-control @error('codigoP') is-invalid @enderror" 
                               value="{{ old('codigoP') }}" placeholder="Ej: PROD001">
                        @error('codigoP')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Categoría -->
                    <div class="col-md-4">
                        <label for="categoria_producto_id" class="form-label fw-semibold">
                            <i class="fas fa-tags text-muted"></i> Categoría <span class="text-danger">*</span>
                        </label>
                        <select id="categoria_producto_id" name="categoria_producto_id" 
                                class="form-select @error('categoria_producto_id') is-invalid @enderror">
                            <option value="">Seleccionar categoría</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}"
                                    {{ old('categoria_producto_id') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombreCP }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria_producto_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Género -->
                    <div class="col-md-4">
                        <label for="producto_genero_id" class="form-label fw-semibold">
                            <i class="fas fa-venus-mars text-muted"></i> Género <span class="text-danger">*</span>
                        </label>
                        <select id="producto_genero_id" name="producto_genero_id" 
                                class="form-select @error('producto_genero_id') is-invalid @enderror">
                            <option value="">Seleccionar género</option>
                            @foreach ($generos as $genero)
                                <option value="{{ $genero->id }}"
                                    {{ old('producto_genero_id') == $genero->id ? 'selected' : '' }}>
                                    {{ $genero->descripcion }}
                                </option>
                            @endforeach
                        </select>
                        @error('producto_genero_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="col-md-8">
                        <label for="descripcionP" class="form-label fw-semibold">
                            <i class="fas fa-align-left text-muted"></i> Descripción <span class="text-danger">*</span>
                        </label>
                        <textarea id="descripcionP" name="descripcionP" rows="3"
                                  class="form-control @error('descripcionP') is-invalid @enderror" 
                                  placeholder="Descripción detallada del producto">{{ old('descripcionP') }}</textarea>
                        @error('descripcionP')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Precio -->
                    <div class="col-md-4">
                        <label for="precioP" class="form-label fw-semibold">
                            <i class="fas fa-dollar-sign text-muted"></i> Precio (S/) <span class="text-danger">*</span>
                        </label>
                        <input id="precioP" name="precioP" type="number" step="0.01" min="0"
                               class="form-control @error('precioP') is-invalid @enderror" 
                               value="{{ old('precioP') }}" placeholder="0.00">
                        @error('precioP')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Imagen -->
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3 mt-3" style="color: #2C2C2C;">
                            <i class="fas fa-image" style="color: #D4AF37;"></i> Imagen del Producto
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <label for="imagenP" class="form-label fw-semibold">
                            <i class="fas fa-upload text-muted"></i> Subir Imagen
                        </label>
                        <input id="imagenP" name="imagenP" type="file" accept="image/*"
                               class="form-control @error('imagenP') is-invalid @enderror">
                        <small class="text-muted">Formatos: JPG, PNG, WEBP (Opcional)</small>
                        @error('imagenP')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones de acción -->
                    <div class="col-12 mt-4">
                        <div class="alert alert-info border-0" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> El stock del producto se actualizará automáticamente al registrar una compra.
                        </div>
                        <div class="d-flex gap-2 justify-content-end border-top pt-3">
                            <a href="{{ route('productos.index') }}" class="btn btn-light px-4">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn px-4" style="background: #D4AF37; color: white;">
                                <i class="fas fa-save"></i> Registrar Producto
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación de precio
        document.getElementById('precioP').addEventListener('input', function(e) {
            if (this.value < 0) this.value = 0;
        });
    </script>
@stop

