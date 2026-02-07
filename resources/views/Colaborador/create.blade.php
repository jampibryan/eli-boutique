@extends('adminlte::page')

@section('title', 'Colaboradores')

@section('content_header')
@stop

@section('content')
    <!-- Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1" style="color: #2C2C2C; font-weight: 700;">
                    <i class="fas fa-user-tie" style="color: #D4AF37;"></i> Registrar Colaborador
                </h3>
                <p class="text-muted mb-0">Complete los datos del nuevo colaborador</p>
            </div>
            <a href="{{ route('colaboradores.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <div class="card shadow-sm" style="border: none; border-top: 4px solid #17a2b8;">
        <div class="card-body p-4">
            <form action="{{ route('colaboradores.store') }}" method="POST">
                @csrf

                <div class="row g-4">
                    <!-- Información Laboral -->
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3" style="color: #2C2C2C;">
                            <i class="fas fa-briefcase" style="color: #17a2b8;"></i> Información Laboral
                        </h5>
                    </div>

                    <!-- Cargo -->
                    <div class="col-md-12">
                        <label for="cargo_id" class="form-label fw-semibold">
                            <i class="fas fa-user-tag text-muted"></i> Cargo <span class="text-danger">*</span>
                        </label>
                        <select id="cargo_id" name="cargo_id" class="form-select @error('cargo_id') is-invalid @enderror">
                            <option value="">Seleccionar cargo</option>
                            @foreach ($cargos as $cargo)
                                <option value="{{ $cargo->id }}" {{ old('cargo_id') == $cargo->id ? 'selected' : '' }}>
                                    {{ $cargo->descripcionCargo }}
                                </option>
                            @endforeach
                        </select>
                        @error('cargo_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Información Personal -->
                    <div class="col-12 mt-4">
                        <h5 class="border-bottom pb-2 mb-3" style="color: #2C2C2C;">
                            <i class="fas fa-user" style="color: #17a2b8;"></i> Información Personal
                        </h5>
                    </div>

                    <!-- Nombre -->
                    <div class="col-md-6">
                        <label for="nombreColab" class="form-label fw-semibold">
                            <i class="fas fa-signature text-muted"></i> Nombre <span class="text-danger">*</span>
                        </label>
                        <input id="nombreColab" name="nombreColab" type="text"
                            class="form-control @error('nombreColab') is-invalid @enderror" value="{{ old('nombreColab') }}"
                            placeholder="Ingrese el nombre">
                        @error('nombreColab')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Apellidos -->
                    <div class="col-md-6">
                        <label for="apellidosColab" class="form-label fw-semibold">
                            <i class="fas fa-signature text-muted"></i> Apellidos <span class="text-danger">*</span>
                        </label>
                        <input id="apellidosColab" name="apellidosColab" type="text"
                            class="form-control @error('apellidosColab') is-invalid @enderror"
                            value="{{ old('apellidosColab') }}" placeholder="Ingrese los apellidos">
                        @error('apellidosColab')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Género -->
                    <div class="col-md-4">
                        <label for="tipo_genero_id" class="form-label fw-semibold">
                            <i class="fas fa-venus-mars text-muted"></i> Género <span class="text-danger">*</span>
                        </label>
                        <select id="tipo_genero_id" name="tipo_genero_id"
                            class="form-select @error('tipo_genero_id') is-invalid @enderror">
                            <option value="">Seleccionar género</option>
                            @foreach ($generos as $genero)
                                <option value="{{ $genero->id }}"
                                    {{ old('tipo_genero_id') == $genero->id ? 'selected' : '' }}>
                                    {{ $genero->descripcionTG }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_genero_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- DNI -->
                    <div class="col-md-4">
                        <label for="dniColab" class="form-label fw-semibold">
                            <i class="fas fa-id-card text-muted"></i> DNI <span class="text-danger">*</span>
                        </label>
                        <input id="dniColab" name="dniColab" type="text" maxlength="8"
                            class="form-control @error('dniColab') is-invalid @enderror" value="{{ old('dniColab') }}"
                            placeholder="12345678">
                        @error('dniColab')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Edad -->
                    <div class="col-md-4">
                        <label for="edadColab" class="form-label fw-semibold">
                            <i class="fas fa-calendar-alt text-muted"></i> Edad <span class="text-danger">*</span>
                        </label>
                        <input id="edadColab" name="edadColab" type="number" min="18" max="99"
                            class="form-control @error('edadColab') is-invalid @enderror" value="{{ old('edadColab') }}"
                            placeholder="18">
                        @error('edadColab')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Información de Contacto -->
                    <div class="col-12 mt-4">
                        <h5 class="border-bottom pb-2 mb-3" style="color: #2C2C2C;">
                            <i class="fas fa-address-book" style="color: #17a2b8;"></i> Información de Contacto
                        </h5>
                    </div>

                    <!-- Correo -->
                    <div class="col-md-6">
                        <label for="correoColab" class="form-label fw-semibold">
                            <i class="fas fa-envelope text-muted"></i> Correo Electrónico <span
                                class="text-danger">*</span>
                        </label>
                        <input id="correoColab" name="correoColab" type="email"
                            class="form-control @error('correoColab') is-invalid @enderror"
                            value="{{ old('correoColab') }}" placeholder="ejemplo@correo.com">
                        @error('correoColab')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div class="col-md-6">
                        <label for="telefonoColab" class="form-label fw-semibold">
                            <i class="fas fa-phone text-muted"></i> Teléfono <span class="text-danger">*</span>
                        </label>
                        <input id="telefonoColab" name="telefonoColab" type="text" maxlength="9"
                            class="form-control @error('telefonoColab') is-invalid @enderror"
                            value="{{ old('telefonoColab') }}" placeholder="987654321">
                        @error('telefonoColab')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones de acción -->
                    <div class="col-12 mt-4">
                        <div class="d-flex gap-2 justify-content-end border-top pt-3">
                            <a href="{{ route('colaboradores.index') }}" class="btn btn-light px-4">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn px-4" style="background: #17a2b8; color: white;">
                                <i class="fas fa-save"></i> Registrar Colaborador
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .form-label {
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.625rem 0.875rem;
            transition: all 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #17a2b8;
            box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.15);
        }
    </style>
@stop

@section('js')
    <script>
        // Validación de solo números para DNI y teléfono
        document.getElementById('dniColab').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        document.getElementById('telefonoColab').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        document.getElementById('edadColab').addEventListener('input', function(e) {
            if (this.value < 18) this.value = 18;
            if (this.value > 99) this.value = 99;
        });
    </script>
@stop
