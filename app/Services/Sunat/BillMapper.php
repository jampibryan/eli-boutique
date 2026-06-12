<?php

namespace App\Services\Sunat;

use App\Models\Venta;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;

class BillMapper
{
    /**
     * Mapear un modelo Venta a un objeto Invoice de Greenter
     */
    public function mapVentaToInvoice(Venta $venta): Invoice
    {
        $invoice = new Invoice();

        // 1. Determinar tipo de comprobante, serie y correlativo
        $descripcionComprobante = $venta->pago->comprobante->descripcionCOM ?? 'Boleta';
        $esFactura = (strcasecmp($descripcionComprobante, 'Factura') === 0);

        $tipoDoc = $esFactura ? '01' : '03'; // 01 = Factura, 03 = Boleta
        $serie = $esFactura ? 'F001' : 'B001';
        $correlativo = intval($venta->codigoVenta);

        $invoice->setTipoDoc($tipoDoc)
            ->setSerie($serie)
            ->setCorrelativo($correlativo)
            ->setFechaEmision(new \DateTime($venta->created_at))
            ->setTipoMoneda('PEN');

        // 2. Configurar emisor (Eli Boutique)
        $companyConfig = config('sunat')[config('sunat.mode')];
        $company = new Company();
        $company->setRuc($companyConfig['ruc'])
            ->setRazonSocial('ELI BOUTIQUE E.I.R.L')
            ->setNombreComercial('Eli Boutique')
            ->setAddress((new Address())
                ->setUbigueo('130802') // Pacanga, Chepén, La Libertad
                ->setDepartamento('La Libertad')
                ->setProvincia('Chepén')
                ->setDistrito('Pacanga')
                ->setDireccion('Calle Ayacucho 624'));
        $invoice->setCompany($company);

        // 3. Configurar cliente
        $client = new Client();
        
        if ($esFactura && !empty($venta->ruc_factura)) {
            $tipoDocCliente = '6'; // RUC
            $docCliente = trim($venta->ruc_factura);
            $nombreCliente = trim($venta->razon_social_factura);
        } else {
            $docCliente = trim($venta->cliente->dniCliente ?? '');
            $tipoDocCliente = '0'; // Documento sin clasificar por defecto

            if (strlen($docCliente) === 11) {
                $tipoDocCliente = '6'; // RUC
            } elseif (strlen($docCliente) === 8) {
                $tipoDocCliente = '1'; // DNI
            }

            // Si no hay documento o es vacío, se usa guion (Venta Anónima/Sin RUC)
            if (empty($docCliente)) {
                $docCliente = '00000000';
                $tipoDocCliente = '1'; // Caer a DNI por defecto en boletas sin DNI
            }

            $nombreCliente = trim(($venta->cliente->nombreCliente ?? '') . ' ' . ($venta->cliente->apellidoCliente ?? ''));
            if (empty($nombreCliente)) {
                $nombreCliente = 'CLIENTE VARIOS';
            }
        }

        $client->setTipoDoc($tipoDocCliente)
            ->setNumDoc($docCliente)
            ->setRznSocial($nombreCliente);

        $invoice->setClient($client);

        // 4. Totales tributarios
        $montoTotal = floatval($venta->montoTotal);
        $igv = floatval($venta->IGV);
        $subTotal = floatval($venta->subTotal);

        $invoice->setMtoOperGravadas($subTotal) // Operaciones Gravadas (Base Imponible)
            ->setMtoIGV($igv)                   // Sumatoria IGV
            ->setTotalImpuestos($igv)           // Total de impuestos
            ->setMtoImpVenta($montoTotal);      // Importe Total (Venta)

        // 5. Configurar detalles de la venta
        $details = [];
        foreach ($venta->detalles as $item) {
            $detail = new SaleDetail();

            $cantidad = floatval($item->cantidad);
            $subtotalLinea = floatval($item->subtotal_linea);

            // Precio Unitario (con IGV) y Valor Unitario (sin IGV)
            $precioUnitario = $cantidad > 0 ? ($subtotalLinea / $cantidad) : 0;
            $valorUnitario = $precioUnitario / 1.18;
            $valorVenta = $subtotalLinea / 1.18;
            $igvLinea = $subtotalLinea - $valorVenta;

            $nombreProd = $item->producto->nombreProd ?? 'Prenda de Vestir';
            $tallaStr = $item->talla->descripcionTalla ?? '';
            if (!empty($tallaStr)) {
                $nombreProd .= ' - Talla: ' . $tallaStr;
            }

            $detail->setCodProducto($item->producto->codigoProd ?? 'PROD' . $item->producto_id)
                ->setUnidad('NIU') // Unidad física
                ->setCantidad($cantidad)
                ->setDescripcion($nombreProd)
                ->setMtoValorUnitario($valorUnitario)
                ->setMtoValorVenta($valorVenta)
                ->setMtoBaseIgv($valorVenta)
                ->setPorcentajeIgv(18.0)
                ->setIgv($igvLinea)
                ->setMtoPrecioUnitario($precioUnitario)
                ->setTipAfeIgv('10') // Afecto - Gravado Operación Onerosa
                ->setTotalImpuestos($igvLinea);

            $details[] = $detail;
        }

        $invoice->setDetails($details);

        // 6. Leyenda de monto en letras
        $legend = new Legend();
        $legend->setCode('1000') // Código de monto en letras
            ->setValue($this->convertirMontoALetras($montoTotal));
        
        $invoice->setLegends([$legend]);

        return $invoice;
    }

    /**
     * Convertir número a su representación textual en Soles
     */
    public function convertirMontoALetras(float $monto): string
    {
        $enteros = intval($monto);
        $centavos = intval(round(($monto - $enteros) * 100));
        
        $textoCentavos = str_pad($centavos, 2, '0', STR_PAD_LEFT) . '/100 SOLES';
        
        if ($enteros === 0) {
            return 'CERO CON ' . $textoCentavos;
        }

        $letras = $this->num2letras($enteros);
        return strtoupper($letras) . ' CON ' . $textoCentavos;
    }

    private function num2letras(int $num): string
    {
        $unidades = ['', 'un', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'];
        $decenas = ['', 'diez', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'];
        $dieces = ['diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve'];
        $veintes = ['veinte', 'veintiuno', 'veintidós', 'veintitrés', 'veinticuatro', 'veinticinco', 'veintiséis', 'veintisieste', 'veintiocho', 'veintinueve'];
        $centenas = ['', 'cien', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'];

        if ($num < 0) return 'menos ' . $this->num2letras(abs($num));
        
        if ($num < 10) return $unidades[$num];
        
        if ($num < 20) return $dieces[$num - 10];
        
        if ($num < 30) return $veintes[$num - 20];
        
        if ($num < 100) {
            $d = intval($num / 10);
            $u = $num % 10;
            return $decenas[$d] . ($u > 0 ? ' y ' . $unidades[$u] : '');
        }
        
        if ($num < 1000) {
            $c = intval($num / 100);
            $resto = $num % 100;
            if ($num == 100) return 'cien';
            if ($c == 1) return 'ciento ' . $this->num2letras($resto);
            return $centenas[$c] . ($resto > 0 ? ' ' . $this->num2letras($resto) : '');
        }
        
        if ($num < 1000000) {
            $m = intval($num / 1000);
            $resto = $num % 1000;
            $m_str = ($m == 1) ? 'mil' : $this->num2letras($m) . ' mil';
            return $m_str . ($resto > 0 ? ' ' . $this->num2letras($resto) : '');
        }

        return 'monto demasiado grande';
    }
}
