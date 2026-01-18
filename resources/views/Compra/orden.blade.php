<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Compra #{{ $compra->codigoCompra }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            padding: 15px;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .header-grid {
            display: table;
            width: 100%;
        }

        .header-left, .header-right {
            display: table-cell;
            vertical-align: middle;
        }

        .header-left {
            width: 65%;
        }

        .header-right {
            width: 35%;
            text-align: right;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .company-slogan {
            font-size: 10px;
            opacity: 0.9;
            font-style: italic;
        }

        .doc-type {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .doc-number {
            font-size: 16px;
            font-weight: bold;
            background: rgba(255,255,255,0.2);
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
        }

        /* Info sections */
        .info-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .info-row {
            margin-bottom: 3px;
            line-height: 1.4;
        }

        .info-label {
            font-weight: 600;
            color: #28a745;
            display: inline-block;
            min-width: 70px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 2px solid #28a745;
        }

        /* Tabla de productos */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        th {
            background: #28a745;
            color: white;
            padding: 6px 4px;
            text-align: center;
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
        }

        td {
            padding: 5px 4px;
            border: 1px solid #dee2e6;
            text-align: center;
            font-size: 9px;
        }

        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .text-left {
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }

        .product-name {
            font-weight: 600;
            color: #2c3e50;
        }

        /* Totales */
        .totals-section {
            margin-top: 10px;
            display: table;
            width: 100%;
        }

        .totals-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }

        .totals-right {
            display: table-cell;
            width: 50%;
        }

        .notes-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 8px;
        }

        .notes-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 4px;
            font-size: 9px;
        }

        .notes-content {
            color: #856404;
            line-height: 1.4;
            font-size: 9px;
        }

        .totals-table {
            width: 100%;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .totals-table tr:last-child {
            background: #28a745;
            color: white;
            font-weight: bold;
        }

        .totals-table td {
            padding: 5px 8px;
            border: none;
            border-bottom: 1px solid #dee2e6;
        }

        .totals-table tr:last-child td {
            border-bottom: none;
        }

        .totals-label {
            text-align: right;
            font-weight: 600;
        }

        .totals-value {
            text-align: right;
            font-weight: 600;
        }

        /* Footer */
        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 8px;
            color: #6c757d;
        }

        .signature-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 5px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin: 40px 20px 5px 20px;
            padding-top: 5px;
            font-weight: 600;
            font-size: 9px;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 600;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .two-col {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .col-50 {
            display: table-cell;
            width: 50%;
            padding: 0 5px;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-grid">
            <div class="header-left">
                <div class="company-name">ELI BOUTIQUE</div>
                <div class="company-slogan">Moda que inspira, calidad que perdura</div>
            </div>
            <div class="header-right">
                <div class="doc-type">ORDEN DE COMPRA</div>
                <div class="doc-number">#{{ $compra->codigoCompra }}</div>
            </div>
        </div>
    </div>

    <!-- Información básica -->
    <div class="info-section">
        <div class="two-col">
            <div class="col-50">
                <div class="section-title">INFORMACION DE LA ORDEN</div>
                <div class="info-row">
                    <span class="info-label">Fecha:</span>
                    {{ \Carbon\Carbon::parse($compra->created_at)->format('d/m/Y') }}
                </div>
                @if($compra->fecha_envio)
                <div class="info-row">
                    <span class="info-label">Envío:</span>
                    {{ \Carbon\Carbon::parse($compra->fecha_envio)->format('d/m/Y') }}
                </div>
                @endif
                @if($compra->comprobante)
                <div class="info-row">
                    <span class="info-label">Comprobante:</span>
                    {{ $compra->comprobante->descripcionCOM }}
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Estado:</span>
                    <span class="badge 
                        @if($compra->estadoTransaccion->descripcionET == 'Borrador') badge-warning
                        @elseif($compra->estadoTransaccion->descripcionET == 'Enviada') badge-info
                        @else badge-success
                        @endif">
                        {{ $compra->estadoTransaccion->descripcionET }}
                    </span>
                </div>
            </div>
            <div class="col-50">
                <div class="section-title">NUESTRA EMPRESA</div>
                <div class="info-row">
                    <span class="info-label">Empresa:</span>
                    ELI BOUTIQUE E.I.R.L
                </div>
                <div class="info-row">
                    <span class="info-label">Dirección:</span>
                    Ayacucho #624 - Pacanga
                </div>
                <div class="info-row">
                    <span class="info-label">Contacto:</span>
                    {{ $colaborador->nombreColab }} {{ $colaborador->apellidosColab }}
                </div>
                <div class="info-row">
                    <span class="info-label">Teléfono:</span>
                    {{ $colaborador->telefonoColab }}
                </div>
            </div>
        </div>
    </div>

    <!-- Proveedor y Pago -->
    <div class="info-section">
        <div class="two-col">
            <div class="col-50">
                <div class="section-title">PROVEEDOR</div>
                <div class="info-row">
                    <span class="info-label">Empresa:</span>
                    {{ $compra->proveedor->nombreEmpresa }}
                </div>
                <div class="info-row">
                    <span class="info-label">Contacto:</span>
                    {{ $compra->proveedor->nombreProveedor }} {{ $compra->proveedor->apellidoProveedor }}
                </div>
                <div class="info-row">
                    <span class="info-label">Direccion:</span>
                    {{ $compra->proveedor->direccionProveedor }}
                </div>
                <div class="info-row">
                    <span class="info-label">Telefono:</span>
                    {{ $compra->proveedor->telefonoProveedor }}
                </div>
                @if($compra->proveedor->correoProveedor)
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    {{ $compra->proveedor->correoProveedor }}
                </div>
                @endif
            </div>
            @if($compra->estadoTransaccion->descripcionET != 'Borrador' && $compra->estadoTransaccion->descripcionET != 'Enviada')
            <div class="col-50">
                <div class="section-title">CONDICIONES</div>
                <div class="info-row">
                    <span class="info-label">Pago:</span>
                    <strong>{{ $compra->condiciones_pago ?? 'Contra entrega' }}</strong>
                </div>
                @if($compra->fecha_entrega_estimada)
                <div class="info-row">
                    <span class="info-label">Entrega:</span>
                    {{ \Carbon\Carbon::parse($compra->fecha_entrega_estimada)->format('d/m/Y') }}
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Productos -->
    <div class="section-title">DETALLE DE PRODUCTOS</div>
    <table>
        <thead>
            <tr>
                <th style="width: 6%;">#</th>
                <th style="width: 12%;">CÓDIGO</th>
                <th style="width: 38%;" class="text-left">PRODUCTO</th>
                <th style="width: 10%;">TALLA</th>
                <th style="width: 10%;">CANT.</th>
                @if($compra->estadoTransaccion->descripcionET != 'Borrador' && $compra->estadoTransaccion->descripcionET != 'Enviada')
                <th style="width: 12%;">P. UNIT.</th>
                <th style="width: 12%;">SUBTOTAL</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($compra->detalles as $index => $detalle)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $detalle->producto->codigoP }}</td>
                <td class="text-left">
                    <span class="product-name">{{ $detalle->producto->descripcionP }}</span>
                </td>
                <td><strong>{{ $detalle->talla->descripcion }}</strong></td>
                <td><strong>{{ $detalle->cantidad }}</strong></td>
                @if($compra->estadoTransaccion->descripcionET != 'Borrador' && $compra->estadoTransaccion->descripcionET != 'Enviada')
                <td class="text-right">S/. {{ number_format($detalle->precio_cotizado ?? $detalle->precio_final ?? 0, 2) }}</td>
                <td class="text-right"><strong>S/. {{ number_format($detalle->subtotal_linea ?? 0, 2) }}</strong></td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totales -->
    @if($compra->estadoTransaccion->descripcionET != 'Borrador' && $compra->estadoTransaccion->descripcionET != 'Enviada')
    <div class="totals-section">
        <div class="totals-left">
            @if($compra->notas_proveedor)
            <div class="notes-box">
                <div class="notes-title">NOTAS / OBSERVACIONES</div>
                <div class="notes-content">{{ $compra->notas_proveedor }}</div>
            </div>
            @endif
        </div>
        <div class="totals-right">
            <table class="totals-table">
                <tr>
                    <td class="totals-label">Subtotal:</td>
                    <td class="totals-value">S/. {{ number_format($compra->subtotal, 2) }}</td>
                </tr>
                @if($compra->descuento > 0)
                <tr>
                    <td class="totals-label">Descuento:</td>
                    <td class="totals-value" style="color: #dc3545;">- S/. {{ number_format($compra->descuento, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td class="totals-label">IGV (18%):</td>
                    <td class="totals-value">S/. {{ number_format($compra->igv, 2) }}</td>
                </tr>
                <tr>
                    <td class="totals-label">TOTAL:</td>
                    <td class="totals-value">S/. {{ number_format($compra->total, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>
    @endif

    <!-- Firmas -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">Firma del Proveedor</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Firma Autorizada - Eli Boutique</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <strong>ELI BOUTIQUE E.I.R.L</strong><br>
        Ayacucho #624 - Pacanga | Tel: {{ $colaborador->telefonoColab }}<br>
        Generado: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
