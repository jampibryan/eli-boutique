@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
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
                                <button type="button" class="btn btn-card-delete w-100" 
                                    onclick="confirmarEliminacion('{{ $cliente->id }}', '{{ addslashes($cliente->nombreCliente . ' ' . $cliente->apellidoCliente) }}', 'cliente')">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                                <form id="form-eliminar-cliente-{{ $cliente->id }}" 
                                    action="{{ route('clientes.destroy', $cliente) }}" 
                                    method="post" style="display: none;">
                                    @csrf
                                    @method('delete')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 shadow-sm mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>¡Atención!</strong> Esta acción marcará el registro como eliminado.
                    </div>
                    
                    <p class="mb-3">Estás a punto de eliminar:</p>
                    <div class="alert alert-light border shadow-sm">
                        <strong id="nombreElemento" class="text-danger"></strong>
                    </div>
                    
                    <p class="mb-2"><strong>Para confirmar, escribe:</strong></p>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-keyboard"></i>
                        </span>
                        <input type="text" id="confirmacionTexto" class="form-control" 
                            placeholder="Escribe ELIMINAR" autocomplete="off">
                    </div>
                    
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        Los registros históricos seguirán mostrando esta información.
                    </small>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarEliminar" disabled>
                        <i class="fas fa-trash-alt"></i> Confirmar Eliminación
                    </button>
                </div>
            </div>
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
        let modalEliminar;
        let elementoActual = { id: null, tipo: null };

        document.addEventListener('DOMContentLoaded', function() {
            modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'));
            
            // Habilitar/deshabilitar botón según el texto ingresado
            document.getElementById('confirmacionTexto').addEventListener('input', function() {
                const texto = this.value.trim().toUpperCase();
                document.getElementById('btnConfirmarEliminar').disabled = texto !== 'ELIMINAR';
            });

            // Confirmar eliminación
            document.getElementById('btnConfirmarEliminar').addEventListener('click', function() {
                document.getElementById(`form-eliminar-${elementoActual.tipo}-${elementoActual.id}`).submit();
            });

            // Limpiar modal al cerrarse
            document.getElementById('modalEliminar').addEventListener('hidden.bs.modal', function() {
                document.getElementById('confirmacionTexto').value = '';
                document.getElementById('btnConfirmarEliminar').disabled = true;
            });
        });

        function confirmarEliminacion(id, nombre, tipo) {
            elementoActual = { id, tipo };
            document.getElementById('nombreElemento').textContent = nombre;
            modalEliminar.show();
            // Focus en el input
            setTimeout(() => document.getElementById('confirmacionTexto').focus(), 500);
        }

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
