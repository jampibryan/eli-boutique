<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Compra;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReporteGraficoController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware para verificar permisos
        $this->middleware('permission:ver reportes gráficos');
    }

    public function index()
    {
        // return view('reporte.index');
    }

    public function ventas(Request $request)
    {
        // Obtener los parámetros de la solicitud
        Log::info($request);
        $mesInicio = $request->input('mesInicio');
        $mesFinal = $request->input('mesFinal');
        $diaInicio = $request->input('diaInicio');
        $diaFinal = $request->input('diaFinal');
        $ventas = [];

        $labelsMes = [];
        $valuesMes = [];
        $labelsDia = [];
        $valuesDia = [];

        if ($mesInicio && $mesFinal) {
            // Filtrar las ventas por rango de meses y estado de transacción "Pagado"
            $ventas = Venta::whereHas('estadoTransaccion', function ($query) {
                $query->where('descripcionET', 'Pagado');
            })
                ->whereBetween('created_at', [$mesInicio . '-01', $mesFinal . '-31'])
                ->get();

            // Procesar las ventas para los meses
            foreach ($ventas as $venta) {
                $mes = \Carbon\Carbon::parse($venta->created_at)->format('Y-m'); // Agrupar por mes
                if (!isset($labelsMes[$mes])) {
                    $labelsMes[$mes] = $mes;
                    $valuesMes[$mes] = 0; // Inicializa el conteo de ventas
                }
                $valuesMes[$mes] += $venta->montoTotal;
            }

            // Ordenar los meses para que estén en orden ascendente
            ksort($labelsMes);
            ksort($valuesMes);
        } elseif ($diaInicio && $diaFinal) {
            // Asegurarse de incluir todo el día final
            $diaFinal = \Carbon\Carbon::parse($diaFinal)->endOfDay(); // Cambia el día final a las 23:59:59

            // Filtrar las ventas por rango de días y estado de transacción "Pagado"
            $ventas = Venta::whereHas('estadoTransaccion', function ($query) {
                $query->where('descripcionET', 'Pagado');
            })
                ->whereBetween('created_at', [$diaInicio, $diaFinal])
                ->get();

            // Procesar las ventas para los días
            foreach ($ventas as $venta) {
                $fecha = \Carbon\Carbon::parse($venta->created_at)->format('Y-m-d'); // Agrupar por día
                if (!isset($labelsDia[$fecha])) {
                    $labelsDia[$fecha] = $fecha;
                    $valuesDia[$fecha] = 0; // Inicializa el conteo de ventas
                }
                $valuesDia[$fecha] += $venta->montoTotal;
            }

            // Ordenar los días para que estén en orden ascendente
            ksort($labelsDia);
            ksort($valuesDia);
        }

        // Convertir los datos en arrays para pasarlos a la vista
        $labelsMes = array_values($labelsMes);
        $valuesMes = array_values($valuesMes);
        $labelsDia = array_values($labelsDia);
        $valuesDia = array_values($valuesDia);

        log::info($labelsDia);
        log::info($valuesDia);
        log::info($labelsMes);
        log::info($valuesMes);

        return view('Reporte.VentaGrafico', compact('labelsMes', 'valuesMes', 'labelsDia', 'valuesDia'));
    }

    public function generarPdfVentasDia(Request $request)
    {
        // Validar entradas
        $diaInicio = $request->input('diaInicio');
        $diaFinal = Carbon::parse($request->input('diaFinal'))->endOfDay();
        $chartImage = $request->input('chartImage'); // Recibir la imagen base64

        if (!$diaInicio || !$diaFinal) {
            abort(400, 'El rango de fechas es obligatorio.');
        }

        if (!$chartImage || !str_starts_with($chartImage, 'data:image/')) {
            abort(400, 'La imagen del gráfico no es válida.');
        }

        // Filtrar las ventas por rango de fechas
        $ventas = Venta::whereHas('estadoTransaccion', function ($query) {
            $query->where('descripcionET', 'Pagado');
        })
            ->whereBetween('created_at', [$diaInicio, $diaFinal])
            ->get();

        $labelsDia = [];
        $valuesDia = [];

        foreach ($ventas as $venta) {
            $fecha = Carbon::parse($venta->created_at)->format('Y-m-d');
            if (!isset($valuesDia[$fecha])) {
                $labelsDia[] = $fecha;
                $valuesDia[$fecha] = 0;
            }
            $valuesDia[$fecha] += $venta->montoTotal;
        }

        // Procesar la imagen base64 para incluirla en el PDF
        $chartImageData = str_replace('data:image/png;base64,', '', $chartImage);
        $chartImageData = base64_decode($chartImageData);

        // Guardar temporalmente la imagen para pasarla al PDF (opcional, según librería)
        $tempImagePath = storage_path('app/public/temp_chart_image.png');
        file_put_contents($tempImagePath, $chartImageData);

        // Generar el PDF con la imagen y los datos
        $pdf = Pdf::loadView('Reporte.ventaspdf', [
            'ventas' => $ventas,
            'labelsDia' => $labelsDia,
            'valuesDia' => $valuesDia,
            'chartImage' => $tempImagePath, // Pasar la ruta de la imagen al PDF
        ]);

        // Eliminar la imagen temporal después de usarla
        //unlink($tempImagePath);

        return $pdf->stream('Reporte_Ventas.pdf');
    }




    public function compras(Request $request)
    {
        // Obtener los parámetros de la solicitud
        $mesInicio = $request->input('mesInicio');
        $mesFinal = $request->input('mesFinal');
        $diaInicio = $request->input('diaInicio');
        $diaFinal = $request->input('diaFinal');
        $compras = [];

        $labelsMes = [];
        $valuesMes = [];
        $labelsDia = [];
        $valuesDia = [];

        if ($mesInicio && $mesFinal) {
            // Filtrar las compras por rango de meses y estado de transacción "Pagado" o "Recibido"
            $compras = Compra::whereHas('estadoTransaccion', function ($query) {
                $query->whereIn('descripcionET', ['Pagado', 'Recibido']);
            })
                ->whereBetween('created_at', [$mesInicio . '-01', $mesFinal . '-31'])
                ->with('pago') // Asegurarse de incluir la relación de pagos
                ->get();

            // Procesar las compras para los meses
            foreach ($compras as $compra) {
                $mes = \Carbon\Carbon::parse($compra->created_at)->format('Y-m'); // Agrupar por mes
                if (!isset($labelsMes[$mes])) {
                    $labelsMes[$mes] = $mes;
                    $valuesMes[$mes] = 0; // Inicializa el conteo de compras
                }

                // Sumar el importe del pago relacionado con la compra
                if ($compra->pago) {
                    $valuesMes[$mes] += $compra->pago->importe; // Usar el importe del pago
                }
            }

            // Ordenar los para que estén en orden ascendente
            ksort($labelsMes);
            ksort($valuesMes);
        } elseif ($diaInicio && $diaFinal) {
            // Asegurarse de incluir todo el día final
            $diaFinal = \Carbon\Carbon::parse($diaFinal)->endOfDay(); // Cambia el día final a las 23:59:59

            // Filtrar las compras por rango de días y estado de transacción "Pagado" o "Recibido"
            $compras = Compra::whereHas('estadoTransaccion', function ($query) {
                $query->whereIn('descripcionET', ['Pagado', 'Recibido']);
            })
                ->whereBetween('created_at', [$diaInicio, $diaFinal])
                ->with('pago') // Asegurarse de incluir la relación de pagos
                ->get();

            // Procesar las compras para los días
            foreach ($compras as $compra) {
                $fecha = \Carbon\Carbon::parse($compra->created_at)->format('Y-m-d'); // Agrupar por día
                if (!isset($labelsDia[$fecha])) {
                    $labelsDia[$fecha] = $fecha;
                    $valuesDia[$fecha] = 0; // Inicializa el conteo de Compras
                }

                // Sumar el importe del pago relacionado con la compra
                if ($compra->pago) {
                    $valuesDia[$fecha] += $compra->pago->importe; // Usar el importe del pago
                }
            }

            // Ordenar los días para que estén en orden ascendente
            ksort($labelsDia);
            ksort($valuesDia);
        }

        // Convertir los datos en arrays para pasarlos a la vista
        $labelsMes = array_values($labelsMes);
        $valuesMes = array_values($valuesMes);
        $labelsDia = array_values($labelsDia);
        $valuesDia = array_values($valuesDia);

        return view('Reporte.CompraGrafico', compact('labelsMes', 'valuesMes', 'labelsDia', 'valuesDia'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
