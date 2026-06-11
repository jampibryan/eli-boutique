<?php

namespace App\Services\Sunat;

use Greenter\Model\Sale\Invoice;
use Greenter\See;

class SunatService
{
    protected $see;
    protected $config;

    public function __construct()
    {
        $this->config = config('sunat');
        $this->see = new See();

        // Cargar certificado digital y clave privada
        $certPath = $this->config['certificate']['path'];
        $keyPath = $this->config['certificate']['key_path'];

        if (!file_exists($certPath)) {
            throw new \Exception("No se encontró el certificado digital en: " . $certPath);
        }

        if (!file_exists($keyPath)) {
            throw new \Exception("No se encontró la llave privada en: " . $keyPath);
        }

        $certPem = file_get_contents($certPath) . "\n" . file_get_contents($keyPath);

        $this->see->setCertificate($certPem);

        // Configurar credenciales (RUC + Usuario SOL, Clave SOL)
        $activeConfig = $this->config[$this->config['mode']];
        $this->see->setCredentials(
            $activeConfig['ruc'] . $activeConfig['usuario'],
            $activeConfig['clave']
        );

        // Configurar endpoint de SUNAT
        $this->see->setService($activeConfig['endpoint']);
    }

    /**
     * Firmar y enviar factura/boleta
     */
    public function sendInvoice(Invoice $invoice)
    {
        return $this->see->send($invoice);
    }

    /**
     * Obtener el XML firmado
     */
    public function getXmlSigned(Invoice $invoice): string
    {
        return $this->see->getXmlSigned($invoice);
    }
}
