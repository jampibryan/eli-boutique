@php
    use Illuminate\Support\Facades\Auth;

    $isFactura = (strcasecmp($tipoComprobante, 'Factura') === 0);
    $usuario = Auth::user();
    $clienteNombre = trim(($venta->cliente->nombreCliente ?? '') . ' ' . ($venta->cliente->apellidoCliente ?? ''));
    if (empty($clienteNombre)) {
        $clienteNombre = 'CLIENTE VARIOS';
    }
    $baseImponible = $venta->detalles->sum(fn($d) => $d->cantidad * $d->base_imponible);
    $igvTotal = $venta->detalles->sum(fn($d) => $d->cantidad * $d->igv);
    $cajero = $venta->colaborador->nombreColab ?? ($usuario->name ?? 'Caja Central');
    $importe = $venta->pago ? (float) $venta->pago->importe : (float) $venta->montoTotal;
    $vuelto = $venta->pago ? (float) $venta->pago->vuelto : 0.00;
    $rucEmpresa = '20608976543';
    $codigoSerie = ($isFactura ? 'F001-' : 'B001-') . $venta->codigoVenta;

    // Logo en base64 para evitar bloqueos de red en DomPDF
    $logoBase64 = '';
    $logoPath = public_path('img/logo_eli_boutique.png');
    if (file_exists($logoPath)) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $isFactura ? 'Factura' : 'Boleta' }} {{ $venta->codigoVenta }}</title>
    <style>
        @page {
            margin: 3mm 3mm;
            size: 80mm auto;
        }

        body {
            font-family: DejaVu Sans, monospace;
            color: #000000;
            font-size: 8.5pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        .ticket-wrapper {
            width: 100%;
            margin: 0 auto;
            text-align: center;
        }

        .logo {
            max-width: 60px;
            height: auto;
            margin: 0 auto 4px auto;
            display: block;
        }

        .brand-title {
            font-size: 12.5pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 2px 0;
        }

        .brand-slogan {
            font-size: 7.5pt;
            font-weight: normal;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .brand-info {
            font-size: 7.5pt;
            line-height: 1.25;
            margin-bottom: 5px;
        }

        .divider {
            border-bottom: 1px dashed #000000;
            margin: 4px 0;
        }

        .divider-double {
            border-bottom: 2px dashed #000000;
            margin: 5px 0;
        }

        .doc-title {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0;
        }

        .doc-number {
            font-size: 10.5pt;
            font-weight: bold;
            margin-bottom: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table {
            font-size: 7.8pt;
            text-align: left;
            margin: 3px 0;
        }

        .meta-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        .meta-label {
            font-weight: bold;
            width: 40%;
        }

        .items-table {
            font-size: 7.5pt;
            margin: 4px 0;
            text-align: left;
        }

        .items-table th {
            font-weight: bold;
            border-bottom: 1px dashed #000000;
            padding: 3px 0;
            text-transform: uppercase;
            font-size: 7.5pt;
        }

        .items-table td {
            padding: 3px 0;
            vertical-align: top;
            border-bottom: 1px dotted #bbb;
        }

        .col-cant {
            width: 12%;
            text-align: center;
        }

        .col-desc {
            width: 48%;
            padding-right: 3px;
        }

        .col-pu {
            width: 20%;
            text-align: right;
            white-space: nowrap;
        }

        .col-tot {
            width: 20%;
            text-align: right;
            font-weight: bold;
            white-space: nowrap;
        }

        .item-talla {
            font-size: 7pt;
            color: #333;
        }

        .items-table .col-pu {
            padding-right: 6px;
        }

        .totals-table {
            font-size: 8pt;
            text-align: right;
            margin: 4px 0;
        }

        .totals-table td {
            padding: 2px 0;
        }

        .grand-total {
            font-size: 10.5pt;
            font-weight: bold;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }

        .grand-total td {
            padding: 4px 0;
        }

        .footer {
            text-align: center;
            font-size: 7pt;
            line-height: 1.3;
            margin-top: 8px;
        }
    </style>
</head>

<body>
    <div class="ticket-wrapper">
        @if (!empty($logoBase64))
            <img src="{{ $logoBase64 }}" alt="Eli Boutique" class="logo">
        @endif

        <div class="brand-title">ELI BOUTIQUE</div>
        <div class="brand-slogan">Exclusividad, Moda y Elegancia</div>
        <div class="brand-info">
            R.U.C.: {{ $rucEmpresa }}<br>
            Jr. Comercio N° 456 - Huánuco, Perú<br>
            Tel: (062) 512345 &bull; Cel: 987 654 321
        </div>

        <div class="divider-double"></div>

        <div class="doc-title">{{ $isFactura ? 'FACTURA' : 'BOLETA DE VENTA' }}</div>
        <div class="doc-number">{{ $codigoSerie }}</div>

        <div class="divider"></div>

        <table class="meta-table">
            <tr>
                <td class="meta-label">FECHA EMISIÓN:</td>
                <td>{{ $venta->created_at->format('d/m/Y h:i A') }}</td>
            </tr>
            <tr>
                <td class="meta-label">CAJERO/EMISOR:</td>
                <td>{{ strtoupper($cajero) }}</td>
            </tr>
            <tr>
                <td class="meta-label">ESTADO:</td>
                <td><strong>{{ strtoupper($venta->estadoTransaccion->descripcionET ?? 'PAGADO') }}</strong></td>
            </tr>
            @if ($isFactura)
                <tr>
                    <td class="meta-label">R.U.C.:</td>
                    <td><strong>{{ $venta->ruc_factura ?? ($venta->cliente->dniCliente ?? '-') }}</strong></td>
                </tr>
                <tr>
                    <td class="meta-label">RAZÓN SOCIAL:</td>
                    <td><strong>{{ strtoupper($venta->razon_social_factura ?? $clienteNombre) }}</strong></td>
                </tr>
                @if($venta->direccion_factura)
                <tr><td>DIRECCIÓN:</td><td>{{ $venta->direccion_factura }}</td></tr>
                @endif
            @else
                <tr>
                    <td class="meta-label">CLIENTE:</td>
                    <td><strong>{{ strtoupper($clienteNombre) }}</strong></td>
                </tr>
                <tr>
                    <td class="meta-label">DNI / DOC:</td>
                    <td>{{ $venta->cliente->dniCliente ?? 'SIN DOCUMENTO' }}</td>
                </tr>
            @endif
        </table>

        <div class="divider"></div>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-cant">CNT</th>
                    <th class="col-desc">DESCRIPCIÓN</th>
                    <th class="col-pu">P.U.</th>
                    <th class="col-tot">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($venta->detalles as $detalle)
                    @php
                        $tallaNombre = $detalle->talla->descripcion ?? ($detalle->producto->tallas->first()->descripcion ?? '-');
                        $prodNombre = $detalle->producto->descripcionP ?? 'Prenda';
                    @endphp
                    <tr>
                        <td class="col-cant">{{ $detalle->cantidad }}</td>
                        <td class="col-desc">
                            {{ $prodNombre }}<br>
                            <span class="item-talla">[Talla: {{ $tallaNombre }}]</span>
                        </td>
                        <td class="col-pu">S/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="col-tot">S/. {{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <table class="totals-table">
            @if ($isFactura)
                <tr>
                    <td>OP. GRAVADA:</td>
                    <td style="width: 35%;">S/. {{ number_format($baseImponible, 2) }}</td>
                </tr>
                <tr>
                    <td>I.G.V. (18%):</td>
                    <td>S/. {{ number_format($igvTotal, 2) }}</td>
                </tr>
            @endif
            <tr class="grand-total">
                <td>TOTAL A PAGAR:</td>
                <td>S/. {{ number_format($venta->montoTotal, 2) }}</td>
            </tr>
            <tr>
                <td>IMPORTE RECIBIDO:</td>
                <td>S/. {{ number_format($importe, 2) }}</td>
            </tr>
            <tr>
                <td><strong>VUELTO:</strong></td>
                <td><strong>S/. {{ number_format($vuelto, 2) }}</strong></td>
            </tr>
        </table>

        <div class="divider-double"></div>

        <div class="footer">
            <strong>¡GRACIAS POR SU PREFERENCIA!</strong><br>
            ELI BOUTIQUE &bull; EXCLUSIVIDAD Y ESTILO<br>
            Conserve este comprobante para cualquier cambio dentro de 7 días.<br>
            Comprobante emitido desde el sistema interno.
        </div>
    </div>
</body>

</html>
