@php
    $isFactura = (strcasecmp($tipoComprobante, 'Factura') === 0);
    $clienteNombre = trim(($venta->cliente->nombreCliente ?? '') . ' ' . ($venta->cliente->apellidoCliente ?? ''));
    if (empty($clienteNombre)) {
        $clienteNombre = 'CLIENTE VARIOS';
    }
    $baseImponible = $venta->detalles->sum(fn($d) => $d->cantidad * $d->base_imponible);
    $igvTotal = $venta->detalles->sum(fn($d) => $d->cantidad * $d->igv);
    $cajero = $venta->colaborador->nombreColab ?? (auth()->user()->name ?? 'Caja Central');
    $importe = $venta->pago ? (float) $venta->pago->importe : (float) $venta->montoTotal;
    $vuelto = $venta->pago ? (float) $venta->pago->vuelto : 0.00;
    $rucEmpresa = '20608976543';
    $codigoSerie = ($isFactura ? 'F001-' : 'B001-') . $venta->codigoVenta;

    // Cadena QR oficial estándar SUNAT
    $qrData = implode('|', [
        $rucEmpresa,
        $isFactura ? '01' : '03',
        $isFactura ? 'F001' : 'B001',
        $venta->codigoVenta,
        number_format($igvTotal, 2, '.', ''),
        number_format($venta->montoTotal, 2, '.', ''),
        $venta->created_at->format('Y-m-d'),
        $isFactura ? '6' : '1',
        $isFactura ? ($venta->ruc_factura ?? '') : ($venta->cliente->dniCliente ?? ''),
    ]);
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket_{{ $isFactura ? 'Factura' : 'Boleta' }}_{{ $venta->codigoVenta }}</title>

    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- QRCode.js para renderizado 100% offline -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        :root {
            --ticket-width: 380px;
            --ticket-font-size: 11.5px;
            --brand-dark: #1e1e1e;
            --brand-gold: #c59b27;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 12px;
        }

        /* Toolbar Superior (Estilo Chavín Prime) */
        .ticket-toolbar {
            width: 100%;
            max-width: var(--ticket-width);
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px 16px;
            margin-bottom: 18px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            transition: max-width 0.25s ease;
        }

        .toolbar-brand {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .toolbar-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #334155;
        }

        .format-selector {
            display: inline-flex;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 2px;
            gap: 2px;
        }

        .btn-format {
            border: none;
            background: transparent;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            cursor: pointer;
            color: #64748b;
            transition: all 0.2s;
        }

        .btn-format.active {
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-print {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        }

        .btn-print:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
            color: white;
        }

        .btn-pdf {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #334155;
        }

        .btn-pdf:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .btn-close {
            background: #f1f5f9;
            color: #64748b;
            padding: 7px 10px;
        }

        .btn-close:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        kbd {
            font-family: 'JetBrains Mono', monospace;
            font-size: 9.5px;
            background: rgba(0, 0, 0, 0.12);
            padding: 2px 5px;
            border-radius: 4px;
            font-weight: 600;
        }

        /* Contenedor del Ticket Imprimible */
        .printable-ticket-area {
            width: 100%;
            max-width: var(--ticket-width);
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 22px 18px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            font-family: 'JetBrains Mono', monospace;
            font-size: var(--ticket-font-size);
            line-height: 1.4;
            color: #000000;
            transition: max-width 0.25s ease, padding 0.25s ease;
        }

        /* 58mm Narrow Preset */
        body.mode-58mm {
            --ticket-width: 280px;
            --ticket-font-size: 9.5px;
        }

        body.mode-58mm .printable-ticket-area {
            padding: 12px 10px;
        }

        body.mode-58mm .ticket-logo {
            max-width: 60px;
        }

        body.mode-58mm .brand-title {
            font-size: 16px;
        }

        /* Encabezado */
        .ticket-header {
            text-align: center;
            margin-bottom: 10px;
        }

        .ticket-logo {
            max-width: 75px;
            height: auto;
            margin: 0 auto 8px auto;
            display: block;
            filter: grayscale(100%) contrast(150%);
        }

        .brand-title {
            font-family: 'Inter', sans-serif;
            font-size: 19px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #000;
            margin-bottom: 2px;
        }

        .brand-slogan {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #333;
            margin-bottom: 6px;
        }

        .brand-info {
            font-size: 10.5px;
            color: #222;
            line-height: 1.35;
        }

        .ticket-divider {
            border-top: 1px dashed #444;
            margin: 8px 0;
            width: 100%;
        }

        .ticket-divider-double {
            border-top: 2px dashed #000;
            margin: 10px 0;
            width: 100%;
        }

        /* Caja de Identificación de Documento */
        .doc-title {
            text-align: center;
            font-weight: 800;
            font-size: 13.5px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 4px 0 2px 0;
        }

        .doc-number {
            text-align: center;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        /* Tabla de Metadatos */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
            font-size: inherit;
        }

        .meta-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        .meta-label {
            font-weight: 700;
            width: 42%;
            color: #111;
        }

        .meta-value {
            font-weight: 500;
            text-align: left;
            word-break: break-word;
        }

        /* Tabla de Productos / Prendas */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
            font-size: inherit;
        }

        .items-table th {
            font-weight: 800;
            padding: 4px 0;
            text-align: left;
            border-bottom: 1px dashed #333;
            font-size: 10.5px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .item-row {
            border-bottom: 1px dotted #ccc;
        }

        .item-name {
            font-weight: 700;
            color: #000;
        }

        .item-size-badge {
            display: inline-block;
            font-weight: 800;
            background: #eee;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 9.5px;
            margin-top: 2px;
        }

        .col-cant {
            width: 12%;
            text-align: center;
            font-weight: 700;
        }

        .col-desc {
            width: 48%;
            padding-right: 4px;
        }

        .col-price {
            width: 20%;
            text-align: right;
            white-space: nowrap;
        }

        .col-total {
            width: 20%;
            text-align: right;
            font-weight: 700;
            white-space: nowrap;
        }

        /* Totales y Finanzas */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
            font-size: inherit;
        }

        .totals-table td {
            padding: 2px 0;
        }

        .totals-label {
            text-align: right;
            padding-right: 12px;
            font-weight: 600;
        }

        .totals-value {
            text-align: right;
            font-weight: 700;
            white-space: nowrap;
            width: 35%;
        }

        .grand-total {
            font-size: 14.5px;
            font-weight: 900;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }

        .grand-total td {
            padding: 6px 0;
        }

        .change-row td {
            font-weight: 800;
        }

        /* Código QR */
        .qr-section {
            text-align: center;
            margin: 12px 0 8px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        #qrcode canvas,
        #qrcode img {
            margin: 0 auto;
            display: block;
        }

        .qr-caption {
            font-size: 9px;
            margin-top: 4px;
            color: #555;
            text-align: center;
        }

        /* Pie de Ticket */
        .ticket-footer {
            text-align: center;
            margin-top: 10px;
            font-size: 10px;
            line-height: 1.4;
            color: #222;
        }

        .footer-highlight {
            font-weight: 800;
            font-size: 11px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        /* REGLAS ESTRICTAS DE IMPRESIÓN TICKET TÉRMICO (CHAVÍN PRIME PALLETICKETS) */
        @media print {
            @page {
                margin: 0 !important;
                size: auto !important;
            }

            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                width: 100% !important;
                height: auto !important;
            }

            body * {
                visibility: hidden !important;
            }

            .ticket-toolbar {
                display: none !important;
            }

            .printable-ticket-area,
            .printable-ticket-area * {
                visibility: visible !important;
            }

            .printable-ticket-area {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
                box-sizing: border-box !important;
                width: var(--ticket-width) !important;
                max-width: var(--ticket-width) !important;
                padding: 4mm 3mm !important;
            }

            body.mode-58mm .printable-ticket-area {
                width: 58mm !important;
                max-width: 58mm !important;
                padding: 2mm !important;
            }
        }
    </style>
</head>

<body>

    <!-- Toolbar Superior (Visible en pantalla, oculto en impresión) -->
    <div class="ticket-toolbar" id="ticketToolbar">
        <div class="toolbar-brand">
            <span class="toolbar-badge">
                <i class="fas fa-receipt me-1"></i> {{ $isFactura ? 'Factura' : 'Boleta' }} #{{ $venta->codigoVenta }}
            </span>

            <!-- Selector de Ancho: 80mm vs 58mm -->
            <div class="format-selector">
                <button type="button" class="btn-format active" id="btnFormat80" onclick="setPaperFormat('80mm')" title="Ticketera estándar de 80mm">
                    80mm
                </button>
                <button type="button" class="btn-format" id="btnFormat58" onclick="setPaperFormat('58mm')" title="Ticketera angosta / portátil de 58mm">
                    58mm
                </button>
            </div>
        </div>

        <div class="toolbar-actions">
            <button type="button" class="btn-action btn-print" onclick="window.print()" title="Imprimir Ticket (Enter o Ctrl+P)">
                <i class="fas fa-print"></i> Imprimir
                <kbd class="d-none d-sm-inline">↵</kbd>
            </button>
            <a href="{{ route('ventas.comprobante', $venta->id) }}" target="_blank" class="btn-action btn-pdf" title="Descargar versión en archivo PDF">
                <i class="fas fa-file-pdf text-danger"></i> PDF
            </a>
            <button type="button" class="btn-action btn-close" onclick="window.close()" title="Cerrar pestaña (Esc)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Área Imprimible del Ticket (Ticketera Vertical) -->
    <div class="printable-ticket-area" id="printableTicketArea">

        <!-- Encabezado Boutique -->
        <div class="ticket-header">
            @if(file_exists(public_path('img/logo_eli_boutique.png')))
                <img src="{{ asset('img/logo_eli_boutique.png') }}" alt="Eli Boutique" class="ticket-logo">
            @endif
            <div class="brand-title">ELI BOUTIQUE</div>
            <div class="brand-slogan">Exclusividad, Moda y Elegancia</div>
            <div class="brand-info">
                R.U.C.: {{ $rucEmpresa }}<br>
                Jr. Comercio N° 456 - Huánuco, Perú<br>
                Tel: (062) 512345 &bull; Cel: 987 654 321
            </div>
        </div>

        <div class="ticket-divider-double"></div>

        <!-- Identificación del Comprobante -->
        <div class="doc-title">
            {{ $isFactura ? 'FACTURA' : 'BOLETA DE VENTA' }}
        </div>
        <div class="doc-number">
            {{ $codigoSerie }}
        </div>

        <div class="ticket-divider"></div>

        <!-- Metadatos de la Venta -->
        <table class="meta-table">
            <tr>
                <td class="meta-label">FECHA EMISIÓN:</td>
                <td class="meta-value">{{ $venta->created_at->format('d/m/Y') }} {{ $venta->created_at->format('h:i A') }}</td>
            </tr>
            <tr>
                <td class="meta-label">CAJERO/EMISOR:</td>
                <td class="meta-value">{{ strtoupper($cajero) }}</td>
            </tr>
            <tr>
                <td class="meta-label">CAJA TURNO:</td>
                <td class="meta-value">CAJA #{{ $venta->caja->codigoCaja ?? '01' }}</td>
            </tr>
            <tr>
                <td class="meta-label">ESTADO:</td>
                <td class="meta-value"><strong>{{ strtoupper($venta->estadoTransaccion->descripcionET ?? 'PAGADO') }}</strong></td>
            </tr>
        </table>

        <div class="ticket-divider"></div>

        <!-- Datos del Cliente / Adquiriente -->
        <table class="meta-table">
            @if($isFactura)
                <tr>
                    <td class="meta-label">R.U.C.:</td>
                    <td class="meta-value"><strong>{{ $venta->ruc_factura ?? ($venta->cliente->dniCliente ?? '-') }}</strong></td>
                </tr>
                <tr>
                    <td class="meta-label">RAZÓN SOCIAL:</td>
                    <td class="meta-value"><strong>{{ strtoupper($venta->razon_social_factura ?? $clienteNombre) }}</strong></td>
                </tr>
                @if($venta->direccion_factura)
                <tr><td>DIRECCIÓN:</td><td>{{ $venta->direccion_factura }}</td></tr>
                @endif
                <tr>
                    <td class="meta-label">CONTACTO:</td>
                    <td class="meta-value">{{ strtoupper($clienteNombre) }}</td>
                </tr>
            @else
                <tr>
                    <td class="meta-label">CLIENTE:</td>
                    <td class="meta-value"><strong>{{ strtoupper($clienteNombre) }}</strong></td>
                </tr>
                <tr>
                    <td class="meta-label">DNI / DOC:</td>
                    <td class="meta-value">{{ $venta->cliente->dniCliente ?? 'SIN DOCUMENTO' }}</td>
                </tr>
            @endif
        </table>

        <div class="ticket-divider"></div>

        <!-- Detalle de Prendas -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-cant">CANT</th>
                    <th class="col-desc">DESCRIPCIÓN</th>
                    <th class="col-price">P.U.</th>
                    <th class="col-total">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($venta->detalles as $detalle)
                    @php
                        $tallaNombre = $detalle->talla->descripcion ?? ($detalle->producto->tallas->first()->descripcion ?? '-');
                        $prodNombre = $detalle->producto->descripcionP ?? 'Prenda';
                    @endphp
                    <tr class="item-row">
                        <td class="col-cant">{{ $detalle->cantidad }}</td>
                        <td class="col-desc">
                            <div class="item-name">{{ $prodNombre }}</div>
                            <span class="item-size-badge">TALLA: {{ $tallaNombre }}</span>
                        </td>
                        <td class="col-price">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="col-total">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="ticket-divider"></div>

        <!-- Totales Financieros -->
        <table class="totals-table">
            @if($isFactura)
                <tr>
                    <td class="totals-label">OP. GRAVADA:</td>
                    <td class="totals-value">S/ {{ number_format($baseImponible, 2) }}</td>
                </tr>
                <tr>
                    <td class="totals-label">I.G.V. (18%):</td>
                    <td class="totals-value">S/ {{ number_format($igvTotal, 2) }}</td>
                </tr>
            @endif
            <tr class="grand-total">
                <td class="totals-label">TOTAL A PAGAR:</td>
                <td class="totals-value">S/ {{ number_format($venta->montoTotal, 2) }}</td>
            </tr>
            <tr>
                <td class="totals-label">IMPORTE RECIBIDO:</td>
                <td class="totals-value">S/ {{ number_format($importe, 2) }}</td>
            </tr>
            <tr class="change-row">
                <td class="totals-label">VUELTO ENTREGADO:</td>
                <td class="totals-value">S/ {{ number_format($vuelto, 2) }}</td>
            </tr>
        </table>

        <div class="ticket-divider-double"></div>

        <!-- Código QR Oficial -->
        <div class="qr-section">
            <div id="qrcode"></div>
            <div class="qr-caption">Código de Control SUNAT</div>
        </div>

        <!-- Pie de Ticket y Políticas -->
        <div class="ticket-footer">
            <div class="footer-highlight">¡GRACIAS POR SU PREFERENCIA!</div>
            <div>ELI BOUTIQUE &bull; MODA Y ESTILO</div>
            <div>Todo cambio de prenda se realiza dentro de los 7 días con este ticket intacto.</div>
            <div>Bienes para uso y consumo final.</div>
        </div>

    </div>

    <!-- Scripts de Interacción (Atajos de Teclado + Formato + Auto-Print) -->
    <script>
        // 1. Generar Código QR Nítido
        document.addEventListener('DOMContentLoaded', function() {
            const qrText = @json($qrData);
            const qrContainer = document.getElementById('qrcode');
            
            if (qrContainer && typeof QRCode !== 'undefined') {
                new QRCode(qrContainer, {
                    text: qrText,
                    width: 110,
                    height: 110,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            }

            // Cargar preferencia de papel guardada
            const savedFormat = localStorage.getItem('eli_ticket_format') || '80mm';
            setPaperFormat(savedFormat, false);

            // Auto-print si la URL trae ?autoprint=1
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autoprint') === '1') {
                setTimeout(function() {
                    window.print();
                }, 400);
            }
        });

        // 2. Conmutador de Papel 80mm vs 58mm (Estilo Chavín Prime)
        function setPaperFormat(format, save = true) {
            const btn80 = document.getElementById('btnFormat80');
            const btn58 = document.getElementById('btnFormat58');

            if (format === '58mm') {
                document.body.classList.add('mode-58mm');
                btn58.classList.add('active');
                btn80.classList.remove('active');
            } else {
                document.body.classList.remove('mode-58mm');
                btn80.classList.add('active');
                btn58.classList.remove('active');
            }

            if (save) {
                localStorage.setItem('eli_ticket_format', format);
            }
        }

        // 3. Atajos de Teclado (Enter / Ctrl+P / Esc)
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                window.close();
            } else if (e.key === 'Enter' || ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'p')) {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>

</html>
