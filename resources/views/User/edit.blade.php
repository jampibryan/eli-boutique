@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
@stop

@section('content')
    <!-- Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1" style="color: #2C2C2C; font-weight: 700;">
                    <i class="fas fa-user-edit" style="color: #D4AF37;"></i> Editar Usuario
                </h3>
                <p class="text-muted mb-0">{{ $user->name }} - {{ $user->email }}</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <div class="card shadow-sm" style="border: none; border-top: 4px solid #D4AF37;">
        <div class="card-body p-4">
            <form action="{{ route('users.update', $user->id) }}" method="POST" id="userForm">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- Información del Usuario -->
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3" style="color: #2C2C2C;">
                            <i class="fas fa-user-circle" style="color: #D4AF37;"></i> Información del Usuario
                        </h5>
                    </div>

                    <!-- Nombre -->
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">
                            <i class="fas fa-signature text-muted"></i> Nombre Completo <span class="text-danger">*</span>
                        </label>
                        <input id="name" name="name" type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $user->name) }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">
                            <i class="fas fa-envelope text-muted"></i> Correo Electrónico <span class="text-danger">*</span>
                        </label>
                        <input id="email" name="email" type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $user->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Rol -->
                    <div class="col-md-12">
                        <label for="role" class="form-label fw-semibold">
                            <i class="fas fa-user-tag text-muted"></i> Rol del Usuario <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role">
                            <option value="">Selecciona un rol</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role', $user->getRoleNames()->first()) == $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> El rol determina los permisos de acceso al sistema
                        </small>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Información Adicional -->
                    <div class="col-12 mt-3">
                        <div class="alert alert-info border-0 shadow-sm">
                            <i class="fas fa-calendar-alt me-2"></i>
                            <strong>Usuario registrado:</strong> {{ $user->created_at->format('d/m/Y H:i') }}
                            <br>
                            <i class="fas fa-clock me-2"></i>
                            <strong>Última actualización:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    <!-- Seguridad -->
                    <div class="col-12 mt-4">
                        <h5 class="border-bottom pb-2 mb-3" style="color: #2C2C2C;">
                            <i class="fas fa-lock" style="color: #D4AF37;"></i> Cambiar Contraseña
                        </h5>
                        <div class="alert alert-warning border-0 shadow-sm">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> Deje estos campos en blanco si no desea cambiar la contraseña
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="col-md-6">
                        <label for="password" class="form-label fw-semibold">
                            <i class="fas fa-key text-muted"></i> Nueva Contraseña
                        </label>
                        <div class="input-group">
                            <input id="password" name="password" type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Mínimo 8 caracteres">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label fw-semibold">
                            <i class="fas fa-check-double text-muted"></i> Confirmar Nueva Contraseña
                        </label>
                        <div class="input-group">
                            <input id="password_confirmation" name="password_confirmation" type="password" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   placeholder="Repita la contraseña">
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                <i class="fas fa-eye"></i>
                            </button>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="col-12 mt-4">
                        <div class="d-flex gap-2 justify-content-end border-top pt-3">
                            <a href="{{ route('users.index') }}" class="btn btn-light px-4">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-boutique-gold px-4">
                                <i class="fas fa-save"></i> Actualizar Usuario
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

        .input-group .btn-outline-secondary {
            border-color: #dee2e6;
        }

        .input-group .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            border-color: #D4AF37;
            color: #D4AF37;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        document.getElementById('togglePasswordConfirmation').addEventListener('click', function() {
            const password = document.getElementById('password_confirmation');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
@stop
