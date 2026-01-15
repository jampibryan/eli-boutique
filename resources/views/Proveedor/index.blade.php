@extends('adminlte::page')

@section('title', 'Proveedores')

@section('content_header')
    <!-- <h1>Lista de proveedores</h1> -->
@stop

@section('content')
    <!-- Barra de acciones -->
    <div class="action-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="mb-0" style="color: #2C2C2C;">
                    <i class="fas fa-truck" style="color: #D4AF37;"></i> Gestión de Proveedores
                </h4>
                <small class="text-muted">Total: <span class="badge bg-dark" id="totalProveedores">{{ $proveedores->count() }}</span></small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('proveedores.create') }}" class="btn btn-boutique-gold">
                    <i class="fas fa-plus"></i> Registrar Proveedor
                </a>
                <a href="{{ route('proveedores.pdf') }}" target="_blank" class="btn btn-boutique-dark">
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
            <input type="text" id="buscarProveedor" class="form-control" placeholder="Buscar por empresa, contacto o RUC...">
        </div>
    </div>

    <!-- Grid de proveedores -->
    <div class="container-fluid">
        <div class="row g-3" id="proveedoresGrid">
            @foreach ($proveedores as $proveedor)
                <div class="col-lg-4 col-md-6 col-sm-12 proveedor-item"
                     data-empresa="{{ strtolower($proveedor->nombreEmpresa) }}"
                     data-contacto="{{ strtolower($proveedor->nombreProveedor . ' ' . $proveedor->apellidoProveedor) }}"
                     data-ruc="{{ $proveedor->RUC }}">
                    <div class="boutique-card">
                        <div class="boutique-card-body">
                            <!-- Header con logo empresa -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-circle" style="background: linear-gradient(135deg, #28a745, #5cb85c);">
                                    {{ strtoupper(substr($proveedor->nombreEmpresa, 0, 1)) }}
                                </div>
                                <div style="flex: 1;">
                                    <h5 class="mb-1" style="color: #2C2C2C; font-weight: 700;">
                                        {{ $proveedor->nombreEmpresa }}
                                    </h5>
                                    <small class="text-muted">#{{ str_pad($proveedor->id, 4, '0', STR_PAD_LEFT) }}</small>
                                </div>
                            </div>

                            <!-- Información de la empresa -->
                            <div>
                                <div class="info-row">
                                    <i class="fas fa-user icon"></i>
                                    <span class="label">Contacto:</span>
                                    <span class="value">{{ $proveedor->nombreProveedor }}
                                        {{ $proveedor->apellidoProveedor }}</span>
                                </div>
                                <div class="info-row">
                                    <i class="fas fa-file-invoice icon"></i>
                                    <span class="label">RUC:</span>
                                    <span class="value">{{ $proveedor->RUC }}</span>
                                </div>
                                <div class="info-row">
                                    <i class="fas fa-map-marker-alt icon"></i>
                                    <span class="label">Dirección:</span>
                                    <span class="value text-truncate"
                                        style="max-width: 200px;">{{ $proveedor->direccionProveedor }}</span>
                                </div>
                                <div class="info-row">
                                    <i class="fas fa-envelope icon"></i>
                                    <span class="label">Email:</span>
                                    <span class="value text-truncate"
                                        style="max-width: 200px;">{{ $proveedor->correoProveedor }}</span>
                                </div>
                                <div class="info-row">
                                    <i class="fas fa-phone icon"></i>
                                    <span class="label">Teléfono:</span>
                                    <span class="value">{{ $proveedor->telefonoProveedor }}</span>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="card-actions">
                                <a href="{{ route('proveedores.edit', $proveedor) }}" class="btn btn-card-edit">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('proveedores.destroy', $proveedor) }}" method="post"
                                    style="flex: 1;"
                                    onsubmit="return confirm('¿Eliminar proveedor {{ $proveedor->nombreEmpresa }}?');">
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

        document.getElementById('buscarProveedor').addEventListener('input', function() {
            const searchTerm = normalizeText(this.value.toLowerCase().trim());
            const items = document.querySelectorAll('.proveedor-item');
            let visibleCount = 0;
            
            items.forEach(item => {
                const empresa = normalizeText(item.dataset.empresa);
                const contacto = normalizeText(item.dataset.contacto);
                const ruc = item.dataset.ruc.toLowerCase();
                
                if (searchTerm === '' || empresa.includes(searchTerm) || contacto.includes(searchTerm) || ruc.includes(searchTerm)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            document.getElementById('totalProveedores').textContent = visibleCount;
        });
    </script>
@stop
