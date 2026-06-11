<?php

namespace App\Jobs;

use App\Models\Venta;
use App\Models\VentaDocumentoSunat;
use App\Services\Sunat\SunatService;
use App\Services\Sunat\BillMapper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SendVentaToSunatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $venta;

    // Número máximo de intentos
    public $tries = 3;

    // Segundos a esperar antes de reintentar en caso de fallo temporal
    public $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(Venta $venta)
    {
        $this->venta = $venta;
    }

    /**
     * Execute the job.
     */
    public function handle(SunatService $sunatService, BillMapper $mapper)
    {
        // 1. Obtener o inicializar registro de documento de SUNAT
        $docSunat = $this->venta->documentoSunat;

        if (!$docSunat) {
            $docSunat = VentaDocumentoSunat::create([
                'venta_id' => $this->venta->id,
                'uuid' => (string) Str::uuid(),
                'estado_envio' => 'pendiente',
                'intentos_envio' => 0,
            ]);
        }

        // Si ya está aceptado, no volver a enviar
        if ($docSunat->estado_envio === 'aceptado') {
            return;
        }

        $docSunat->update([
            'estado_envio' => 'enviando',
            'intentos_envio' => $docSunat->intentos_envio + 1,
            'fecha_envio' => now()
        ]);

        try {
            // 2. Mapear venta local a formato de Greenter
            $invoice = $mapper->mapVentaToInvoice($this->venta);

            // Obtener XML firmado
            $xmlSigned = $sunatService->getXmlSigned($invoice);

            // Extraer el hash de la firma digital (DigestValue)
            $signatureHash = $this->extractHashFromXml($xmlSigned);

            // 3. Transmitir a SUNAT SOAP
            $result = $sunatService->sendInvoice($invoice);

            if ($result->isSuccess()) {
                $cdr = $result->getCdrResponse();

                // Nombres de archivo para almacenar localmente en storage
                $xmlName = 'invoices/' . $invoice->getSerie() . '-' . $invoice->getCorrelativo() . '.xml';
                $cdrName = 'cdrs/R-' . $invoice->getSerie() . '-' . $invoice->getCorrelativo() . '.zip';

                // Guardar en disco local protegido (storage/app/invoices/ y storage/app/cdrs/)
                Storage::put($xmlName, $xmlSigned);
                Storage::put($cdrName, $result->getCdrZip());

                $docSunat->update([
                    'estado_envio' => 'aceptado',
                    'xml_path' => $xmlName,
                    'cdr_path' => $cdrName,
                    'signature_hash' => $signatureHash,
                    'codigo_respuesta_sunat' => $cdr->getCode(),
                    'descripcion_respuesta_sunat' => $cdr->getDescription(),
                    'fecha_respuesta' => now()
                ]);
            } else {
                $error = $result->getError();

                // Rechazado por SUNAT (falla de validación en regla de negocio tributaria)
                $docSunat->update([
                    'estado_envio' => 'rechazado',
                    'codigo_respuesta_sunat' => $error->getCode(),
                    'descripcion_respuesta_sunat' => $error->getMessage(),
                    'fecha_respuesta' => now()
                ]);
            }
        } catch (\Throwable $e) {
            // Error de conexión temporal con SUNAT (SoapFault, Timeout, etc.)
            $docSunat->update([
                'estado_envio' => 'error',
                'descripcion_respuesta_sunat' => 'Error de conexión: ' . $e->getMessage()
            ]);

            // Relanzar la excepción para que Laravel aplique el backoff y reintente el job
            throw $e;
        }
    }

    /**
     * Extrae el valor de DigestValue (hash de la firma) de un XML firmado
     */
    private function extractHashFromXml(string $xml): ?string
    {
        $dom = new \DOMDocument();
        if (@$dom->loadXML($xml)) {
            $xpath = new \DOMXPath($dom);
            $xpath->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
            $nodes = $xpath->query('//ds:DigestValue');
            if ($nodes->length > 0) {
                return $nodes->item(0)->nodeValue;
            }
        }
        return null;
    }
}
