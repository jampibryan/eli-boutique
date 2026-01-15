@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <!-- <h1>Lista de Clientes</h1> -->

@stop

@section('content')
    <!-- Barra de acciones -->
    <div class="action-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="mb-0" style="color: #2C2C2C;">
                    <i class="fas fa-users" style="color: #D4AF37;"></i> Gestión de Clientes
                </h4>
                <small class="text-muted">Total: <span class="badge bg-dark" id="totalClientes">{{ $clientes->count() }}</span></small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('clientes.create') }}" class="btn btn-boutique-gold">
                    <i class="fas fa-plus"></i> Registrar Cliente
                </a>
                <a href="{{ route('clientes.pdf') }}" target="_blank" class="btn btn-boutique-dark">
                    <i class="fas fa-file-pdf"></i> Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Barra de búsqueda -->
    <div class="action-bar mt-3">
        <div class="input-group">
            <span class="input-group-text bg-white">
                <i class="fas fa-search" style="color: #D4AF37;"></i>
            </span>
            <input type="text" id="buscarCliente" class="form-control" placeholder="Buscar por nombre completo o DNI...">
        </div>
    </div>

    <!-- Grid de clientes -->
    <div class="container-fluid">
        <div class="row g-3" id="clientesGrid">
            @foreach ($clientes as $cliente)
                <div class="col-lg-4 col-md-6 col-sm-12 cliente-item" 
                     data-nombre="{{ strtolower($cliente->nombreCliente . ' ' . $cliente->apellidoCliente) }}"
                     data-dni="{{ $cliente->dniCliente }}">
                    <div class="boutique-card">
                        <div class="boutique-card-body">
                            <!-- Header con avatar -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-circle">
                                    {{ strtoupper(substr($cliente->nombreCliente, 0, 1)) }}
                                </div>
                                <div style="flex: 1;">
                                    <h5 class="mb-1" style="color: #2C2C2C; font-weight: 700;">
                                        {{ $cliente->nombreCliente }} {{ $cliente->apellidoCliente }}
                                    </h5>
                                    <div class="d-flex gap-2 align-items-center">
                                        <span class="status-badge status-info">
                                            <i
                                                class="fas fa-{{ $cliente->tipoGenero->descripcionTG == 'Masculino' ? 'mars' : 'venus' }}"></i>
                                            {{ $cliente->tipoGenero->descripcionTG }}
                                        </span>
                                        <small class="text-muted">#{{ str_pad($cliente->id, 4, '0', STR_PAD_LEFT) }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de contacto -->
                            <div>
                                <div class="info-row">
                                    <i class="fas fa-id-card icon"></i>
                                    <span class="label">DNI:</span>
                                    <span class="value">{{ $cliente->dniCliente }}</span>
                                </div>
                                <div class="info-row">
                                    <i class="fas fa-envelope icon"></i>
                                    <span class="label">Email:</span>
                                    <span class="value text-truncate"
                                        style="max-width: 200px;">{{ $cliente->correoCliente }}</span>
                                </div>
                                <div class="info-row">
                                    <i class="fas fa-phone icon"></i>
                                    <span class="label">Teléfono:</span>
                                    <span class="value">{{ $cliente->telefonoCliente }}</span>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="card-actions">
                                <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-card-edit">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('clientes.destroy', $cliente) }}" method="post" style="flex: 1;"
                                    onsubmit="return confirm('¿Eliminar cliente {{ $cliente->nombreCliente }}?');">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-card-delete w-100">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
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
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para normalizar texto (eliminar acentos)
        function normalizeText(text) {
            return text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        }

        // Búsqueda en tiempo real
        document.getElementById('buscarCliente').addEventListener('input', function() {
            const searchTerm = normalizeText(this.value.toLowerCase().trim());
            const items = document.querySelectorAll('.cliente-item');
            let visibleCount = 0;
            
            items.forEach(item => {
                const nombre = normalizeText(item.dataset.nombre);
                const dni = item.dataset.dni.toLowerCase();
                
                if (searchTerm === '' || nombre.includes(searchTerm) || dni.includes(searchTerm)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            document.getElementById('totalClientes').textContent = visibleCount;
        });
    </script>
@stop
