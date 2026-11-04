@php
    $documentType = old('tipo_documento', $cliente->tipo_documento ?? 'DNI');
@endphp
<div class="col-md-6">
    <label for="tipo_documento" class="form-label fw-semibold">Tipo de cliente</label>
    <select id="tipo_documento" name="tipo_documento" class="form-select" required>
        <option value="DNI" @selected($documentType === 'DNI')>Persona — DNI</option>
        <option value="RUC" @selected($documentType === 'RUC')>Empresa — RUC</option>
    </select>
    @error('tipo_documento')<div class="text-danger">{{ $message }}</div>@enderror
</div>
<div class="col-md-6">
    <label for="nombreCliente" id="nombreClienteLabel" class="form-label fw-semibold">Nombre / Razón social *</label>
    <input id="nombreCliente" name="nombreCliente" class="form-control" maxlength="255" required value="{{ old('nombreCliente', $cliente->nombreCliente ?? '') }}">
    @error('nombreCliente')<div class="text-danger">{{ $message }}</div>@enderror
</div>
<div class="col-md-6" data-person-field>
    <label for="apellidoCliente" class="form-label fw-semibold">Apellidos *</label>
    <input id="apellidoCliente" name="apellidoCliente" class="form-control" maxlength="255" value="{{ old('apellidoCliente', $cliente->apellidoCliente ?? '') }}">
    @error('apellidoCliente')<div class="text-danger">{{ $message }}</div>@enderror
</div>
<div class="col-md-6" data-person-field>
    <label for="tipo_genero_id" class="form-label fw-semibold">Género *</label>
    <select id="tipo_genero_id" name="tipo_genero_id" class="form-select">
        <option value="">Seleccionar género</option>
        @foreach($generos as $genero)
            <option value="{{ $genero->id }}" @selected((string) old('tipo_genero_id', $cliente->tipo_genero_id ?? '') === (string) $genero->id)>{{ $genero->descripcionTG }}</option>
        @endforeach
    </select>
    @error('tipo_genero_id')<div class="text-danger">{{ $message }}</div>@enderror
</div>
<div class="col-md-6">
    <label for="dniCliente" id="documentoClienteLabel" class="form-label fw-semibold">Documento *</label>
    <input id="dniCliente" name="dniCliente" class="form-control" inputmode="numeric" required value="{{ old('dniCliente', $cliente->dniCliente ?? '') }}">
    @error('dniCliente')<div class="text-danger">{{ $message }}</div>@enderror
</div>
<div class="col-md-6">
    <label for="direccionCliente" class="form-label fw-semibold">Dirección</label>
    <input id="direccionCliente" name="direccionCliente" class="form-control" maxlength="255" value="{{ old('direccionCliente', $cliente->direccionCliente ?? '') }}">
    @error('direccionCliente')<div class="text-danger">{{ $message }}</div>@enderror
</div>
<script src="{{ asset('js/client-identity.js') }}" defer></script>
