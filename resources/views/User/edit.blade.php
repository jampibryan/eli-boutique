@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
    <h1>Editar Usuario</h1>
@stop

@section('content')
<form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') <!-- Esto indica que estamos haciendo una actualización -->

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Nombre -->
    <div class="mb-3">
        <label for="name" class="form-label">Nombre</label>
        <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}">
    </div>

    <!-- Email -->
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}">
    </div>

    <!-- Contraseña (opcional, si deseas que el usuario mantenga la contraseña) -->
    <div class="mb-3">
        <label for="password" class="form-label">Contraseña (opcional)</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="Dejar en blanco si no se desea cambiar">
    </div>

    <!-- Confirmación de contraseña (opcional) -->
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirmar Contraseña (opcional)</label>
        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="Dejar en blanco si no se desea cambiar">
    </div>

    <!-- Cargar roles dinámicamente -->
    <div class="mb-3">
        <label for="role" class="form-label">Seleccionar Rol</label>
        <select class="form-control" id="role" name="role">
            <option value="">Selecciona un rol</option>
            @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ old('role', $user->getRoleNames()->first()) == $role->name ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>
    </div>

    <a href="{{ route('users.index') }}" class="btn btn-danger">Cancelar</a>
    <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
</form>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
@stop
