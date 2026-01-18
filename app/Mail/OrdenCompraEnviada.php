<?php

namespace App\Mail;

use App\Models\Compra;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class OrdenCompraEnviada extends Mailable
{
    use Queueable, SerializesModels;

    public $compra;

    /**
     * Create a new message instance.
     */
    public function __construct(Compra $compra)
    {
        // Cargar las relaciones necesarias
        $this->compra = $compra->load(['detalles.producto', 'detalles.talla', 'proveedor', 'comprobante', 'estadoTransaccion']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Orden de Compra #' . $this->compra->codigoCompra . ' - Eli Boutique',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.compras.orden-enviada',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Generar el PDF
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Compra.orden', ['compra' => $this->compra, 'colaborador' => \App\Models\Colaborador::find(1)]));
        
        return [
            Attachment::fromData(fn () => $pdf->output(), 'Orden-Compra-' . $this->compra->codigoCompra . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
