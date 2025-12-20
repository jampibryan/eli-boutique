<?php

namespace Database\Seeders;

use App\Models\Caja;
use Illuminate\Database\Seeder;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\EstadoTransaccion;
use App\Models\Comprobante;
use App\Models\Pago;
use Carbon\Carbon;

class VentaSeeder extends Seeder
{
    // VENTAS MANUALES - TODOS LOS 25 PRODUCTOS (IDs 1-25)
    private $ventasManuales = [
        // ============ SEMANA 1 (1-4 Oct) - 70 VENTAS ============
        '2025-10-01' => [ // Miércoles - 15 ventas
            ['cliente' => 1, 'hora' => '08:23', 'comprobante' => 'Boleta', 'productos' => [['id' => 1, 'cant' => 2], ['id' => 6, 'cant' => 1]]],
            ['cliente' => 2, 'hora' => '08:47', 'comprobante' => 'Boleta', 'productos' => [['id' => 5, 'cant' => 3]]],
            ['cliente' => 3, 'hora' => '09:18', 'comprobante' => 'Boleta', 'productos' => [['id' => 3, 'cant' => 1], ['id' => 11, 'cant' => 1]]],
            ['cliente' => 4, 'hora' => '09:42', 'comprobante' => 'Boleta', 'productos' => [['id' => 6, 'cant' => 2]]],
            ['cliente' => 5, 'hora' => '10:17', 'comprobante' => 'Factura', 'productos' => [['id' => 4, 'cant' => 3]]],
            ['cliente' => 6, 'hora' => '10:53', 'comprobante' => 'Boleta', 'productos' => [['id' => 2, 'cant' => 2], ['id' => 21, 'cant' => 1]]],
            ['cliente' => 7, 'hora' => '11:28', 'comprobante' => 'Boleta', 'productos' => [['id' => 7, 'cant' => 1]]],
            ['cliente' => 8, 'hora' => '12:04', 'comprobante' => 'Factura', 'productos' => [['id' => 1, 'cant' => 2], ['id' => 8, 'cant' => 1]]],
            ['cliente' => 9, 'hora' => '14:18', 'comprobante' => 'Boleta', 'productos' => [['id' => 5, 'cant' => 2]]],
            ['cliente' => 10, 'hora' => '14:46', 'comprobante' => 'Boleta', 'productos' => [['id' => 9, 'cant' => 3]]],
            ['cliente' => 11, 'hora' => '15:23', 'comprobante' => 'Boleta', 'productos' => [['id' => 10, 'cant' => 2]]],
            ['cliente' => 12, 'hora' => '15:57', 'comprobante' => 'Boleta', 'productos' => [['id' => 12, 'cant' => 1]]],
            ['cliente' => 13, 'hora' => '16:32', 'comprobante' => 'Factura', 'productos' => [['id' => 1, 'cant' => 2], ['id' => 13, 'cant' => 1]]],
            ['cliente' => 14, 'hora' => '16:58', 'comprobante' => 'Boleta', 'productos' => [['id' => 2, 'cant' => 2]]],
            ['cliente' => 15, 'hora' => '17:24', 'comprobante' => 'Boleta', 'productos' => [['id' => 14, 'cant' => 3]]],
        ],

        '2025-10-02' => [ // Jueves - 16 ventas
            ['cliente' => 16, 'hora' => '08:32', 'comprobante' => 'Boleta', 'productos' => [['id' => 15, 'cant' => 2]]],
            ['cliente' => 17, 'hora' => '08:59', 'comprobante' => 'Factura', 'productos' => [['id' => 6, 'cant' => 1], ['id' => 16, 'cant' => 1]]],
            ['cliente' => 18, 'hora' => '09:27', 'comprobante' => 'Boleta', 'productos' => [['id' => 2, 'cant' => 3]]],
            ['cliente' => 19, 'hora' => '09:53', 'comprobante' => 'Boleta', 'productos' => [['id' => 17, 'cant' => 1]]],
            ['cliente' => 20, 'hora' => '10:28', 'comprobante' => 'Factura', 'productos' => [['id' => 18, 'cant' => 3]]],
            ['cliente' => 21, 'hora' => '10:54', 'comprobante' => 'Boleta', 'productos' => [['id' => 19, 'cant' => 2], ['id' => 21, 'cant' => 1]]],
            ['cliente' => 22, 'hora' => '11:29', 'comprobante' => 'Factura', 'productos' => [['id' => 20, 'cant' => 2]]],
            ['cliente' => 23, 'hora' => '11:55', 'comprobante' => 'Boleta', 'productos' => [['id' => 22, 'cant' => 1]]],
            ['cliente' => 24, 'hora' => '14:22', 'comprobante' => 'Boleta', 'productos' => [['id' => 1, 'cant' => 3]]],
            ['cliente' => 25, 'hora' => '14:48', 'comprobante' => 'Boleta', 'productos' => [['id' => 23, 'cant' => 1]]],
            ['cliente' => 26, 'hora' => '15:13', 'comprobante' => 'Boleta', 'productos' => [['id' => 2, 'cant' => 3]]],
            ['cliente' => 27, 'hora' => '15:39', 'comprobante' => 'Boleta', 'productos' => [['id' => 24, 'cant' => 2]]],
            ['cliente' => 28, 'hora' => '16:04', 'comprobante' => 'Boleta', 'productos' => [['id' => 25, 'cant' => 2]]],
            ['cliente' => 29, 'hora' => '16:29', 'comprobante' => 'Boleta', 'productos' => [['id' => 11, 'cant' => 3]]],
            ['cliente' => 30, 'hora' => '16:54', 'comprobante' => 'Factura', 'productos' => [['id' => 6, 'cant' => 1], ['id' => 21, 'cant' => 2]]],
            ['cliente' => 1, 'hora' => '17:19', 'comprobante' => 'Boleta', 'productos' => [['id' => 7, 'cant' => 2]]],
        ],

        '2025-10-03' => [ // Viernes - 18 ventas
            ['cliente' => 2, 'hora' => '08:41', 'comprobante' => 'Boleta', 'productos' => [['id' => 1, 'cant' => 2]]],
            ['cliente' => 3, 'hora' => '09:07', 'comprobante' => 'Boleta', 'productos' => [['id' => 8, 'cant' => 3]]],
            ['cliente' => 4, 'hora' => '09:33', 'comprobante' => 'Factura', 'productos' => [['id' => 3, 'cant' => 3]]],
            ['cliente' => 5, 'hora' => '09:58', 'comprobante' => 'Boleta', 'productos' => [['id' => 4, 'cant' => 1], ['id' => 12, 'cant' => 1]]],
            ['cliente' => 6, 'hora' => '10:24', 'comprobante' => 'Boleta', 'productos' => [['id' => 9, 'cant' => 2]]],
            ['cliente' => 7, 'hora' => '10:49', 'comprobante' => 'Boleta', 'productos' => [['id' => 5, 'cant' => 3]]],
            ['cliente' => 8, 'hora' => '11:14', 'comprobante' => 'Factura', 'productos' => [['id' => 10, 'cant' => 2]]],
            ['cliente' => 9, 'hora' => '11:39', 'comprobante' => 'Boleta', 'productos' => [['id' => 13, 'cant' => 2], ['id' => 22, 'cant' => 1]]],
            ['cliente' => 10, 'hora' => '14:06', 'comprobante' => 'Boleta', 'productos' => [['id' => 14, 'cant' => 2]]],
            ['cliente' => 11, 'hora' => '14:31', 'comprobante' => 'Boleta', 'productos' => [['id' => 15, 'cant' => 1]]],
            ['cliente' => 12, 'hora' => '14:56', 'comprobante' => 'Boleta', 'productos' => [['id' => 16, 'cant' => 1]]],
            ['cliente' => 13, 'hora' => '15:21', 'comprobante' => 'Factura', 'productos' => [['id' => 17, 'cant' => 2]]],
            ['cliente' => 14, 'hora' => '15:46', 'comprobante' => 'Boleta', 'productos' => [['id' => 18, 'cant' => 3]]],
            ['cliente' => 15, 'hora' => '16:11', 'comprobante' => 'Boleta', 'productos' => [['id' => 19, 'cant' => 2]]],
            ['cliente' => 16, 'hora' => '16:36', 'comprobante' => 'Boleta', 'productos' => [['id' => 20, 'cant' => 1]]],
            ['cliente' => 17, 'hora' => '17:01', 'comprobante' => 'Boleta', 'productos' => [['id' => 23, 'cant' => 2]]],
            ['cliente' => 18, 'hora' => '17:26', 'comprobante' => 'Factura', 'productos' => [['id' => 24, 'cant' => 1], ['id' => 25, 'cant' => 3]]],
            ['cliente' => 19, 'hora' => '17:51', 'comprobante' => 'Boleta', 'productos' => [['id' => 6, 'cant' => 2]]],
        ],

        '2025-10-04' => [ // Sábado MAÑANA - 21 ventas
            ['cliente' => 20, 'hora' => '08:27', 'comprobante' => 'Boleta', 'productos' => [['id' => 1, 'cant' => 3]]],
            ['cliente' => 21, 'hora' => '08:53', 'comprobante' => 'Boleta', 'productos' => [['id' => 2, 'cant' => 1]]],
            ['cliente' => 22, 'hora' => '09:18', 'comprobante' => 'Boleta', 'productos' => [['id' => 3, 'cant' => 2]]],
            ['cliente' => 23, 'hora' => '09:43', 'comprobante' => 'Factura', 'productos' => [['id' => 4, 'cant' => 1], ['id' => 11, 'cant' => 3]]],
            ['cliente' => 24, 'hora' => '10:08', 'comprobante' => 'Boleta', 'productos' => [['id' => 5, 'cant' => 2]]],
            ['cliente' => 25, 'hora' => '10:33', 'comprobante' => 'Boleta', 'productos' => [['id' => 6, 'cant' => 2]]],
            ['cliente' => 26, 'hora' => '10:58', 'comprobante' => 'Factura', 'productos' => [['id' => 7, 'cant' => 3]]],
            ['cliente' => 27, 'hora' => '11:23', 'comprobante' => 'Boleta', 'productos' => [['id' => 8, 'cant' => 1]]],
            ['cliente' => 28, 'hora' => '11:48', 'comprobante' => 'Boleta', 'productos' => [['id' => 9, 'cant' => 3]]],
            ['cliente' => 29, 'hora' => '12:13', 'comprobante' => 'Boleta', 'productos' => [['id' => 10, 'cant' => 1]]],
            ['cliente' => 30, 'hora' => '12:38', 'comprobante' => 'Boleta', 'productos' => [['id' => 12, 'cant' => 1]]],
            ['cliente' => 1, 'hora' => '13:03', 'comprobante' => 'Factura', 'productos' => [['id' => 13, 'cant' => 2]]],
            ['cliente' => 2, 'hora' => '14:28', 'comprobante' => 'Boleta', 'productos' => [['id' => 14, 'cant' => 2]]],
            ['cliente' => 3, 'hora' => '14:53', 'comprobante' => 'Boleta', 'productos' => [['id' => 15, 'cant' => 1]]],
            ['cliente' => 4, 'hora' => '15:18', 'comprobante' => 'Factura', 'productos' => [['id' => 16, 'cant' => 1], ['id' => 21, 'cant' => 1]]],
            ['cliente' => 5, 'hora' => '15:43', 'comprobante' => 'Boleta', 'productos' => [['id' => 17, 'cant' => 2]]],
            ['cliente' => 6, 'hora' => '16:08', 'comprobante' => 'Boleta', 'productos' => [['id' => 18, 'cant' => 1]]],
            ['cliente' => 7, 'hora' => '16:33', 'comprobante' => 'Boleta', 'productos' => [['id' => 19, 'cant' => 2]]],
            ['cliente' => 8, 'hora' => '16:58', 'comprobante' => 'Factura', 'productos' => [['id' => 20, 'cant' => 2], ['id' => 22, 'cant' => 1]]],
            ['cliente' => 9, 'hora' => '17:23', 'comprobante' => 'Boleta', 'productos' => [['id' => 23, 'cant' => 3]]],
            ['cliente' => 10, 'hora' => '17:48', 'comprobante' => 'Boleta', 'productos' => [['id' => 24, 'cant' => 1]]],
        ],

        // ============ SEMANA 2 (6-11 Oct) - 55 VENTAS ============
        '2025-10-06' => [ // Lunes - 8 ventas
            ['cliente' => 11, 'hora' => '08:32', 'comprobante' => 'Boleta', 'productos' => [['id' => 1, 'cant' => 2]]],
            ['cliente' => 12, 'hora' => '09:17', 'comprobante' => 'Boleta', 'productos' => [['id' => 2, 'cant' => 1]]],
            ['cliente' => 13, 'hora' => '10:03', 'comprobante' => 'Boleta', 'productos' => [['id' => 6, 'cant' => 1]]],
            ['cliente' => 14, 'hora' => '10:48', 'comprobante' => 'Factura', 'productos' => [['id' => 3, 'cant' => 2]]],
            ['cliente' => 15, 'hora' => '14:22', 'comprobante' => 'Boleta', 'productos' => [['id' => 4, 'cant' => 1]]],
            ['cliente' => 16, 'hora' => '15:07', 'comprobante' => 'Boleta', 'productos' => [['id' => 7, 'cant' => 2]]],
            ['cliente' => 17, 'hora' => '15:52', 'comprobante' => 'Factura', 'productos' => [['id' => 8, 'cant' => 2]]],
            ['cliente' => 18, 'hora' => '16:37', 'comprobante' => 'Boleta', 'productos' => [['id' => 9, 'cant' => 1]]],
        ],

        '2025-10-07' => [ // Martes - 9 ventas
            ['cliente' => 19, 'hora' => '08:41', 'comprobante' => 'Boleta', 'productos' => [['id' => 5, 'cant' => 2]]],
            ['cliente' => 20, 'hora' => '09:26', 'comprobante' => 'Boleta', 'productos' => [['id' => 10, 'cant' => 1]]],
            ['cliente' => 21, 'hora' => '10:11', 'comprobante' => 'Boleta', 'productos' => [['id' => 11, 'cant' => 1]]],
            ['cliente' => 22, 'hora' => '10:56', 'comprobante' => 'Factura', 'productos' => [['id' => 12, 'cant' => 3]]],
            ['cliente' => 23, 'hora' => '14:31', 'comprobante' => 'Boleta', 'productos' => [['id' => 13, 'cant' => 2]]],
            ['cliente' => 24, 'hora' => '15:16', 'comprobante' => 'Boleta', 'productos' => [['id' => 14, 'cant' => 1]]],
            ['cliente' => 25, 'hora' => '16:01', 'comprobante' => 'Boleta', 'productos' => [['id' => 15, 'cant' => 2]]],
            ['cliente' => 26, 'hora' => '16:46', 'comprobante' => 'Factura', 'productos' => [['id' => 16, 'cant' => 1]]],
            ['cliente' => 27, 'hora' => '17:31', 'comprobante' => 'Boleta', 'productos' => [['id' => 17, 'cant' => 1]]],
        ],

        '2025-10-08' => [ // Miércoles - 9 ventas
            ['cliente' => 28, 'hora' => '08:29', 'comprobante' => 'Boleta', 'productos' => [['id' => 18, 'cant' => 2]]],
            ['cliente' => 29, 'hora' => '09:14', 'comprobante' => 'Boleta', 'productos' => [['id' => 19, 'cant' => 1]]],
            ['cliente' => 30, 'hora' => '09:59', 'comprobante' => 'Boleta', 'productos' => [['id' => 20, 'cant' => 2]]],
            ['cliente' => 1, 'hora' => '10:44', 'comprobante' => 'Factura', 'productos' => [['id' => 21, 'cant' => 1]]],
            ['cliente' => 2, 'hora' => '14:19', 'comprobante' => 'Boleta', 'productos' => [['id' => 22, 'cant' => 1]]],
            ['cliente' => 3, 'hora' => '15:04', 'comprobante' => 'Boleta', 'productos' => [['id' => 23, 'cant' => 2]]],
            ['cliente' => 4, 'hora' => '15:49', 'comprobante' => 'Boleta', 'productos' => [['id' => 24, 'cant' => 2]]],
            ['cliente' => 5, 'hora' => '16:34', 'comprobante' => 'Factura', 'productos' => [['id' => 25, 'cant' => 1]]],
            ['cliente' => 6, 'hora' => '17:19', 'comprobante' => 'Boleta', 'productos' => [['id' => 1, 'cant' => 1]]],
        ],

        '2025-10-09' => [ // Jueves - 10 ventas
            ['cliente' => 7, 'hora' => '08:37', 'comprobante' => 'Boleta', 'productos' => [['id' => 2, 'cant' => 3]]],
            ['cliente' => 8, 'hora' => '09:22', 'comprobante' => 'Boleta', 'productos' => [['id' => 3, 'cant' => 2]]],
            ['cliente' => 9, 'hora' => '10:07', 'comprobante' => 'Boleta', 'productos' => [['id' => 4, 'cant' => 2]]],
            ['cliente' => 10, 'hora' => '10:52', 'comprobante' => 'Factura', 'productos' => [['id' => 5, 'cant' => 2]]],
            ['cliente' => 11, 'hora' => '14:27', 'comprobante' => 'Boleta', 'productos' => [['id' => 6, 'cant' => 1]]],
            ['cliente' => 12, 'hora' => '15:12', 'comprobante' => 'Boleta', 'productos' => [['id' => 7, 'cant' => 1]]],
            ['cliente' => 13, 'hora' => '15:57', 'comprobante' => 'Boleta', 'productos' => [['id' => 8, 'cant' => 1]]],
            ['cliente' => 14, 'hora' => '16:42', 'comprobante' => 'Factura', 'productos' => [['id' => 9, 'cant' => 1]]],
            ['cliente' => 15, 'hora' => '17:27', 'comprobante' => 'Boleta', 'productos' => [['id' => 10, 'cant' => 3]]],
            ['cliente' => 16, 'hora' => '17:52', 'comprobante' => 'Boleta', 'productos' => [['id' => 11, 'cant' => 1]]],
        ],

        '2025-10-10' => [ // Viernes - 9 ventas
            ['cliente' => 17, 'hora' => '08:46', 'comprobante' => 'Boleta', 'productos' => [['id' => 12, 'cant' => 2]]],
            ['cliente' => 18, 'hora' => '09:31', 'comprobante' => 'Boleta', 'productos' => [['id' => 13, 'cant' => 1]]],
            ['cliente' => 19, 'hora' => '10:16', 'comprobante' => 'Boleta', 'productos' => [['id' => 14, 'cant' => 1]]],
            ['cliente' => 20, 'hora' => '11:01', 'comprobante' => 'Factura', 'productos' => [['id' => 15, 'cant' => 1]]],
            ['cliente' => 21, 'hora' => '14:36', 'comprobante' => 'Boleta', 'productos' => [['id' => 16, 'cant' => 2]]],
            ['cliente' => 22, 'hora' => '15:21', 'comprobante' => 'Boleta', 'productos' => [['id' => 17, 'cant' => 2]]],
            ['cliente' => 23, 'hora' => '16:06', 'comprobante' => 'Factura', 'productos' => [['id' => 18, 'cant' => 1]]],
            ['cliente' => 24, 'hora' => '16:51', 'comprobante' => 'Boleta', 'productos' => [['id' => 19, 'cant' => 3]]],
            ['cliente' => 25, 'hora' => '17:36', 'comprobante' => 'Boleta', 'productos' => [['id' => 20, 'cant' => 1]]],
        ],

        '2025-10-11' => [ // Sábado MAÑANA - 10 ventas
            ['cliente' => 26, 'hora' => '08:28', 'comprobante' => 'Boleta', 'productos' => [['id' => 21, 'cant' => 2]]],
            ['cliente' => 27, 'hora' => '09:13', 'comprobante' => 'Boleta', 'productos' => [['id' => 22, 'cant' => 1]]],
            ['cliente' => 28, 'hora' => '09:58', 'comprobante' => 'Factura', 'productos' => [['id' => 23, 'cant' => 2]]],
            ['cliente' => 29, 'hora' => '10:43', 'comprobante' => 'Boleta', 'productos' => [['id' => 24, 'cant' => 1]]],
            ['cliente' => 30, 'hora' => '11:28', 'comprobante' => 'Boleta', 'productos' => [['id' => 25, 'cant' => 1]]],
            ['cliente' => 1, 'hora' => '14:03', 'comprobante' => 'Boleta', 'productos' => [['id' => 1, 'cant' => 2]]],
            ['cliente' => 2, 'hora' => '14:48', 'comprobante' => 'Boleta', 'productos' => [['id' => 2, 'cant' => 1]]],
            ['cliente' => 3, 'hora' => '15:33', 'comprobante' => 'Factura', 'productos' => [['id' => 3, 'cant' => 3]]],
            ['cliente' => 4, 'hora' => '16:18', 'comprobante' => 'Boleta', 'productos' => [['id' => 4, 'cant' => 1]]],
            ['cliente' => 5, 'hora' => '17:03', 'comprobante' => 'Boleta', 'productos' => [['id' => 5, 'cant' => 2]]],
        ],

        // ============ SEMANA 3 (13-18 Oct) - 53 VENTAS ============
        '2025-10-13' => [ // Lunes - 8 ventas
            ['cliente' => 6, 'hora' => '08:33', 'comprobante' => 'Boleta', 'productos' => [['id' => 6, 'cant' => 2]]],
            ['cliente' => 7, 'hora' => '09:18', 'comprobante' => 'Boleta', 'productos' => [['id' => 7, 'cant' => 1]]],
            ['cliente' => 8, 'hora' => '10:03', 'comprobante' => 'Boleta', 'productos' => [['id' => 8, 'cant' => 1]]],
            ['cliente' => 9, 'hora' => '10:48', 'comprobante' => 'Factura', 'productos' => [['id' => 9, 'cant' => 1]]],
            ['cliente' => 10, 'hora' => '14:23', 'comprobante' => 'Boleta', 'productos' => [['id' => 10, 'cant' => 1]]],
            ['cliente' => 11, 'hora' => '15:08', 'comprobante' => 'Boleta', 'productos' => [['id' => 11, 'cant' => 2]]],
            ['cliente' => 12, 'hora' => '15:53', 'comprobante' => 'Boleta', 'productos' => [['id' => 12, 'cant' => 2]]],
            ['cliente' => 13, 'hora' => '16:38', 'comprobante' => 'Factura', 'productos' => [['id' => 13, 'cant' => 1]]],
        ],

        '2025-10-14' => [ // Martes - 8 ventas
            ['cliente' => 14, 'hora' => '08:42', 'comprobante' => 'Boleta', 'productos' => [['id' => 14, 'cant' => 2]]],
            ['cliente' => 15, 'hora' => '09:27', 'comprobante' => 'Boleta', 'productos' => [['id' => 15, 'cant' => 1]]],
            ['cliente' => 16, 'hora' => '10:12', 'comprobante' => 'Boleta', 'productos' => [['id' => 16, 'cant' => 1]]],
            ['cliente' => 17, 'hora' => '10:57', 'comprobante' => 'Factura', 'productos' => [['id' => 17, 'cant' => 1]]],
            ['cliente' => 18, 'hora' => '14:32', 'comprobante' => 'Boleta', 'productos' => [['id' => 18, 'cant' => 2]]],
            ['cliente' => 19, 'hora' => '15:17', 'comprobante' => 'Boleta', 'productos' => [['id' => 19, 'cant' => 3]]],
            ['cliente' => 20, 'hora' => '16:02', 'comprobante' => 'Boleta', 'productos' => [['id' => 20, 'cant' => 1]]],
            ['cliente' => 21, 'hora' => '16:47', 'comprobante' => 'Factura', 'productos' => [['id' => 21, 'cant' => 1]]],
        ],

        '2025-10-15' => [ // Miércoles - 8 ventas
            ['cliente' => 22, 'hora' => '08:31', 'comprobante' => 'Boleta', 'productos' => [['id' => 22, 'cant' => 2]]],
            ['cliente' => 23, 'hora' => '09:16', 'comprobante' => 'Boleta', 'productos' => [['id' => 23, 'cant' => 1]]],
            ['cliente' => 24, 'hora' => '10:01', 'comprobante' => 'Boleta', 'productos' => [['id' => 24, 'cant' => 2]]],
            ['cliente' => 25, 'hora' => '10:46', 'comprobante' => 'Factura', 'productos' => [['id' => 25, 'cant' => 1]]],
            ['cliente' => 26, 'hora' => '14:21', 'comprobante' => 'Boleta', 'productos' => [['id' => 1, 'cant' => 2]]],
            ['cliente' => 27, 'hora' => '15:06', 'comprobante' => 'Boleta', 'productos' => [['id' => 2, 'cant' => 2]]],
            ['cliente' => 28, 'hora' => '15:51', 'comprobante' => 'Boleta', 'productos' => [['id' => 3, 'cant' => 2]]],
            ['cliente' => 29, 'hora' => '16:36', 'comprobante' => 'Factura', 'productos' => [['id' => 4, 'cant' => 1]]],
        ],

        '2025-10-16' => [ // Jueves - 9 ventas
            ['cliente' => 30, 'hora' => '08:39', 'comprobante' => 'Boleta', 'productos' => [['id' => 5, 'cant' => 1]]],
            ['cliente' => 1, 'hora' => '09:24', 'comprobante' => 'Boleta', 'productos' => [['id' => 6, 'cant' => 3]]],
            ['cliente' => 2, 'hora' => '10:09', 'comprobante' => 'Boleta', 'productos' => [['id' => 7, 'cant' => 1]]],
            ['cliente' => 3, 'hora' => '10:54', 'comprobante' => 'Factura', 'productos' => [['id' => 8, 'cant' => 3]]],
            ['cliente' => 4, 'hora' => '14:29', 'comprobante' => 'Boleta', 'productos' => [['id' => 9, 'cant' => 1]]],
            ['cliente' => 5, 'hora' => '15:14', 'comprobante' => 'Boleta', 'productos' => [['id' => 10, 'cant' => 1]]],
            ['cliente' => 6, 'hora' => '15:59', 'comprobante' => 'Boleta', 'productos' => [['id' => 11, 'cant' => 1]]],
            ['cliente' => 7, 'hora' => '16:44', 'comprobante' => 'Factura', 'productos' => [['id' => 12, 'cant' => 2]]],
            ['cliente' => 8, 'hora' => '17:29', 'comprobante' => 'Boleta', 'productos' => [['id' => 13, 'cant' => 1]]],
        ],

        '2025-10-17' => [ // Viernes - 10 ventas
            ['cliente' => 9, 'hora' => '08:48', 'comprobante' => 'Boleta', 'productos' => [['id' => 14, 'cant' => 2]]],
            ['cliente' => 10, 'hora' => '09:33', 'comprobante' => 'Boleta', 'productos' => [['id' => 15, 'cant' => 1]]],
            ['cliente' => 11, 'hora' => '10:18', 'comprobante' => 'Boleta', 'productos' => [['id' => 16, 'cant' => 1]]],
            ['cliente' => 12, 'hora' => '11:03', 'comprobante' => 'Factura', 'productos' => [['id' => 17, 'cant' => 1]]],
            ['cliente' => 13, 'hora' => '14:38', 'comprobante' => 'Boleta', 'productos' => [['id' => 18, 'cant' => 2]]],
            ['cliente' => 14, 'hora' => '15:23', 'comprobante' => 'Boleta', 'productos' => [['id' => 19, 'cant' => 3]]],
            ['cliente' => 15, 'hora' => '16:08', 'comprobante' => 'Boleta', 'productos' => [['id' => 20, 'cant' => 1]]],
            ['cliente' => 16, 'hora' => '16:53', 'comprobante' => 'Factura', 'productos' => [['id' => 21, 'cant' => 1]]],
            ['cliente' => 17, 'hora' => '17:38', 'comprobante' => 'Boleta', 'productos' => [['id' => 22, 'cant' => 2]]],
            ['cliente' => 18, 'hora' => '17:53', 'comprobante' => 'Boleta', 'productos' => [['id' => 23, 'cant' => 1]]],
        ],

        '2025-10-18' => [ // Sábado MAÑANA - 10 ventas
            ['cliente' => 19, 'hora' => '08:29', 'comprobante' => 'Boleta', 'productos' => [['id' => 24, 'cant' => 2]]],
            ['cliente' => 20, 'hora' => '09:14', 'comprobante' => 'Boleta', 'productos' => [['id' => 25, 'cant' => 1]]],
            ['cliente' => 21, 'hora' => '09:59', 'comprobante' => 'Boleta', 'productos' => [['id' => 1, 'cant' => 1]]],
            ['cliente' => 22, 'hora' => '10:44', 'comprobante' => 'Factura', 'productos' => [['id' => 2, 'cant' => 3]]],
            ['cliente' => 23, 'hora' => '11:29', 'comprobante' => 'Boleta', 'productos' => [['id' => 3, 'cant' => 1]]],
            ['cliente' => 24, 'hora' => '14:04', 'comprobante' => 'Boleta', 'productos' => [['id' => 4, 'cant' => 2]]],
            ['cliente' => 25, 'hora' => '14:49', 'comprobante' => 'Boleta', 'productos' => [['id' => 5, 'cant' => 1]]],
            ['cliente' => 26, 'hora' => '15:34', 'comprobante' => 'Factura', 'productos' => [['id' => 6, 'cant' => 2]]],
            ['cliente' => 27, 'hora' => '16:19', 'comprobante' => 'Boleta', 'productos' => [['id' => 7, 'cant' => 1]]],
            ['cliente' => 28, 'hora' => '17:04', 'comprobante' => 'Boleta', 'productos' => [['id' => 8, 'cant' => 2]]],
        ],

        // ============ SEMANA 4 (20-25 Oct) - 52 VENTAS ============
        '2025-10-20' => [ // Lunes - 8 ventas
            ['cliente' => 29, 'hora' => '08:34', 'comprobante' => 'Boleta', 'productos' => [['id' => 9, 'cant' => 2]]],
            ['cliente' => 30, 'hora' => '09:19', 'comprobante' => 'Boleta', 'productos' => [['id' => 10, 'cant' => 1]]],
            ['cliente' => 1, 'hora' => '10:04', 'comprobante' => 'Boleta', 'productos' => [['id' => 11, 'cant' => 1]]],
            ['cliente' => 2, 'hora' => '10:49', 'comprobante' => 'Factura', 'productos' => [['id' => 12, 'cant' => 1]]],
            ['cliente' => 3, 'hora' => '14:24', 'comprobante' => 'Boleta', 'productos' => [['id' => 13, 'cant' => 1]]],
            ['cliente' => 4, 'hora' => '15:09', 'comprobante' => 'Boleta', 'productos' => [['id' => 14, 'cant' => 2]]],
            ['cliente' => 5, 'hora' => '15:54', 'comprobante' => 'Boleta', 'productos' => [['id' => 15, 'cant' => 2]]],
            ['cliente' => 6, 'hora' => '16:39', 'comprobante' => 'Factura', 'productos' => [['id' => 16, 'cant' => 1]]],
        ],

        '2025-10-21' => [ // Martes - 8 ventas
            ['cliente' => 7, 'hora' => '08:43', 'comprobante' => 'Boleta', 'productos' => [['id' => 17, 'cant' => 2]]],
            ['cliente' => 8, 'hora' => '09:28', 'comprobante' => 'Boleta', 'productos' => [['id' => 18, 'cant' => 2]]],
            ['cliente' => 9, 'hora' => '10:13', 'comprobante' => 'Boleta', 'productos' => [['id' => 19, 'cant' => 1]]],
            ['cliente' => 10, 'hora' => '10:58', 'comprobante' => 'Factura', 'productos' => [['id' => 20, 'cant' => 1]]],
            ['cliente' => 11, 'hora' => '14:33', 'comprobante' => 'Boleta', 'productos' => [['id' => 21, 'cant' => 2]]],
            ['cliente' => 12, 'hora' => '15:18', 'comprobante' => 'Boleta', 'productos' => [['id' => 22, 'cant' => 3]]],
            ['cliente' => 13, 'hora' => '16:03', 'comprobante' => 'Factura', 'productos' => [['id' => 23, 'cant' => 1]]],
            ['cliente' => 14, 'hora' => '16:48', 'comprobante' => 'Boleta', 'productos' => [['id' => 24, 'cant' => 3]]],
        ],

        '2025-10-22' => [ // Miércoles - 8 ventas
            ['cliente' => 15, 'hora' => '08:32', 'comprobante' => 'Boleta', 'productos' => [['id' => 25, 'cant' => 2]]],
            ['cliente' => 16, 'hora' => '09:17', 'comprobante' => 'Boleta', 'productos' => [['id' => 1, 'cant' => 1]]],
            ['cliente' => 17, 'hora' => '10:02', 'comprobante' => 'Boleta', 'productos' => [['id' => 2, 'cant' => 2]]],
            ['cliente' => 18, 'hora' => '10:47', 'comprobante' => 'Factura', 'productos' => [['id' => 3, 'cant' => 1]]],
            ['cliente' => 19, 'hora' => '14:22', 'comprobante' => 'Boleta', 'productos' => [['id' => 4, 'cant' => 2]]],
            ['cliente' => 20, 'hora' => '15:07', 'comprobante' => 'Boleta', 'productos' => [['id' => 5, 'cant' => 2]]],
            ['cliente' => 21, 'hora' => '15:52', 'comprobante' => 'Boleta', 'productos' => [['id' => 6, 'cant' => 2]]],
            ['cliente' => 22, 'hora' => '16:37', 'comprobante' => 'Factura', 'productos' => [['id' => 7, 'cant' => 1]]],
        ],

        '2025-10-23' => [ // Jueves - 9 ventas
            ['cliente' => 23, 'hora' => '08:40', 'comprobante' => 'Boleta', 'productos' => [['id' => 8, 'cant' => 1]]],
            ['cliente' => 24, 'hora' => '09:25', 'comprobante' => 'Boleta', 'productos' => [['id' => 9, 'cant' => 3]]],
            ['cliente' => 25, 'hora' => '10:10', 'comprobante' => 'Boleta', 'productos' => [['id' => 10, 'cant' => 1]]],
            ['cliente' => 26, 'hora' => '10:55', 'comprobante' => 'Factura', 'productos' => [['id' => 11, 'cant' => 2]]],
            ['cliente' => 27, 'hora' => '14:30', 'comprobante' => 'Boleta', 'productos' => [['id' => 12, 'cant' => 1]]],
            ['cliente' => 28, 'hora' => '15:15', 'comprobante' => 'Boleta', 'productos' => [['id' => 13, 'cant' => 1]]],
            ['cliente' => 29, 'hora' => '16:00', 'comprobante' => 'Factura', 'productos' => [['id' => 14, 'cant' => 1]]],
            ['cliente' => 30, 'hora' => '16:45', 'comprobante' => 'Boleta', 'productos' => [['id' => 15, 'cant' => 2]]],
            ['cliente' => 1, 'hora' => '17:30', 'comprobante' => 'Boleta', 'productos' => [['id' => 16, 'cant' => 1]]],
        ],

        '2025-10-24' => [ // Viernes - 9 ventas
            ['cliente' => 2, 'hora' => '08:49', 'comprobante' => 'Boleta', 'productos' => [['id' => 17, 'cant' => 2]]],
            ['cliente' => 3, 'hora' => '09:34', 'comprobante' => 'Boleta', 'productos' => [['id' => 18, 'cant' => 1]]],
            ['cliente' => 4, 'hora' => '10:19', 'comprobante' => 'Boleta', 'productos' => [['id' => 19, 'cant' => 1]]],
            ['cliente' => 5, 'hora' => '11:04', 'comprobante' => 'Factura', 'productos' => [['id' => 20, 'cant' => 1]]],
            ['cliente' => 6, 'hora' => '14:39', 'comprobante' => 'Boleta', 'productos' => [['id' => 21, 'cant' => 2]]],
            ['cliente' => 7, 'hora' => '15:24', 'comprobante' => 'Boleta', 'productos' => [['id' => 22, 'cant' => 3]]],
            ['cliente' => 8, 'hora' => '16:09', 'comprobante' => 'Factura', 'productos' => [['id' => 23, 'cant' => 1]]],
            ['cliente' => 9, 'hora' => '16:54', 'comprobante' => 'Boleta', 'productos' => [['id' => 24, 'cant' => 1]]],
            ['cliente' => 10, 'hora' => '17:39', 'comprobante' => 'Boleta', 'productos' => [['id' => 25, 'cant' => 2]]],
        ],

        '2025-10-25' => [ // Sábado MAÑANA - 10 ventas
            ['cliente' => 11, 'hora' => '08:30', 'comprobante' => 'Boleta', 'productos' => [['id' => 1, 'cant' => 2]]],
            ['cliente' => 12, 'hora' => '09:15', 'comprobante' => 'Boleta', 'productos' => [['id' => 2, 'cant' => 1]]],
            ['cliente' => 13, 'hora' => '10:00', 'comprobante' => 'Boleta', 'productos' => [['id' => 3, 'cant' => 1]]],
            ['cliente' => 14, 'hora' => '10:45', 'comprobante' => 'Factura', 'productos' => [['id' => 4, 'cant' => 3]]],
            ['cliente' => 15, 'hora' => '11:30', 'comprobante' => 'Boleta', 'productos' => [['id' => 5, 'cant' => 1]]],
            ['cliente' => 16, 'hora' => '14:05', 'comprobante' => 'Boleta', 'productos' => [['id' => 6, 'cant' => 2]]],
            ['cliente' => 17, 'hora' => '14:50', 'comprobante' => 'Boleta', 'productos' => [['id' => 7, 'cant' => 1]]],
            ['cliente' => 18, 'hora' => '15:35', 'comprobante' => 'Factura', 'productos' => [['id' => 8, 'cant' => 2]]],
            ['cliente' => 19, 'hora' => '16:20', 'comprobante' => 'Boleta', 'productos' => [['id' => 9, 'cant' => 1]]],
            ['cliente' => 20, 'hora' => '17:05', 'comprobante' => 'Boleta', 'productos' => [['id' => 10, 'cant' => 2]]],
        ],

        // ============ ÚLTIMA SEMANA (27-31 Oct) - 26 VENTAS ============
        '2025-10-27' => [ // Lunes - 6 ventas
            ['cliente' => 21, 'hora' => '08:35', 'comprobante' => 'Boleta', 'productos' => [['id' => 11, 'cant' => 2]]],
            ['cliente' => 22, 'hora' => '09:20', 'comprobante' => 'Boleta', 'productos' => [['id' => 12, 'cant' => 1]]],
            ['cliente' => 23, 'hora' => '10:05', 'comprobante' => 'Boleta', 'productos' => [['id' => 13, 'cant' => 1]]],
            ['cliente' => 24, 'hora' => '14:40', 'comprobante' => 'Factura', 'productos' => [['id' => 14, 'cant' => 1]]],
            ['cliente' => 25, 'hora' => '15:25', 'comprobante' => 'Boleta', 'productos' => [['id' => 15, 'cant' => 1]]],
            ['cliente' => 26, 'hora' => '16:10', 'comprobante' => 'Boleta', 'productos' => [['id' => 16, 'cant' => 2]]],
        ],

        '2025-10-28' => [ // Martes - 5 ventas
            ['cliente' => 27, 'hora' => '08:44', 'comprobante' => 'Boleta', 'productos' => [['id' => 17, 'cant' => 2]]],
            ['cliente' => 28, 'hora' => '09:29', 'comprobante' => 'Boleta', 'productos' => [['id' => 18, 'cant' => 1]]],
            ['cliente' => 29, 'hora' => '10:14', 'comprobante' => 'Boleta', 'productos' => [['id' => 19, 'cant' => 1]]],
            ['cliente' => 30, 'hora' => '14:49', 'comprobante' => 'Factura', 'productos' => [['id' => 20, 'cant' => 3]]],
            ['cliente' => 1, 'hora' => '15:34', 'comprobante' => 'Boleta', 'productos' => [['id' => 21, 'cant' => 2]]],
        ],

        '2025-10-29' => [ // Miércoles - 5 ventas
            ['cliente' => 2, 'hora' => '08:33', 'comprobante' => 'Boleta', 'productos' => [['id' => 22, 'cant' => 2]]],
            ['cliente' => 3, 'hora' => '09:18', 'comprobante' => 'Boleta', 'productos' => [['id' => 23, 'cant' => 1]]],
            ['cliente' => 4, 'hora' => '10:03', 'comprobante' => 'Factura', 'productos' => [['id' => 24, 'cant' => 2]]],
            ['cliente' => 5, 'hora' => '14:38', 'comprobante' => 'Boleta', 'productos' => [['id' => 25, 'cant' => 1]]],
            ['cliente' => 6, 'hora' => '15:23', 'comprobante' => 'Boleta', 'productos' => [['id' => 1, 'cant' => 2]]],
        ],

        '2025-10-30' => [ // Jueves - 5 ventas
            ['cliente' => 7, 'hora' => '08:41', 'comprobante' => 'Boleta', 'productos' => [['id' => 2, 'cant' => 1]]],
            ['cliente' => 8, 'hora' => '09:26', 'comprobante' => 'Boleta', 'productos' => [['id' => 3, 'cant' => 3]]],
            ['cliente' => 9, 'hora' => '10:11', 'comprobante' => 'Factura', 'productos' => [['id' => 4, 'cant' => 3]]],
            ['cliente' => 10, 'hora' => '14:46', 'comprobante' => 'Boleta', 'productos' => [['id' => 5, 'cant' => 2]]],
            ['cliente' => 11, 'hora' => '15:31', 'comprobante' => 'Boleta', 'productos' => [['id' => 6, 'cant' => 2]]],
        ],

        '2025-10-31' => [ // Viernes - 5 ventas
            ['cliente' => 12, 'hora' => '08:50', 'comprobante' => 'Boleta', 'productos' => [['id' => 7, 'cant' => 2]]],
            ['cliente' => 13, 'hora' => '09:35', 'comprobante' => 'Boleta', 'productos' => [['id' => 8, 'cant' => 1]]],
            ['cliente' => 14, 'hora' => '10:20', 'comprobante' => 'Factura', 'productos' => [['id' => 9, 'cant' => 1]]],
            ['cliente' => 15, 'hora' => '14:55', 'comprobante' => 'Boleta', 'productos' => [['id' => 10, 'cant' => 3]]],
            ['cliente' => 16, 'hora' => '15:40', 'comprobante' => 'Boleta', 'productos' => [['id' => 11, 'cant' => 2]]],
        ],
    ];


    public function run()
    {
        echo "💰 VENTASEEDER CON SISTEMA DE CAJA - 256 ventas controladas\n";
        echo str_repeat("=", 60) . "\n\n";

        // Verificar datos
        $clientes = Cliente::take(30)->get();
        $estadoPagado = EstadoTransaccion::where('descripcionET', 'Pagado')->first();

        if ($clientes->isEmpty() || !$estadoPagado) {
            echo "❌ Faltan datos básicos\n";
            return;
        }

        $ventasCreadas = 0;
        $cajasCreadas = 0;

        // Array para estadísticas finales
        $resumenCajas = [];

        foreach ($this->ventasManuales as $fecha => $ventasDia) {
            $carbonFecha = Carbon::parse($fecha);
            $nombreDia = $carbonFecha->locale('es')->dayName;

            echo "\n📅 {$nombreDia} {$fecha}\n";
            echo str_repeat("-", 40) . "\n";

            // ==================== 1. ABRIR CAJA PARA ESTE DÍA ====================
            echo "🟢 ABRIENDO CAJA... ";

            // Verificar si ya existe caja para esta fecha
            $caja = Caja::where('fecha', $fecha)->first();

            if (!$caja) {
                $caja = Caja::create([
                    'fecha' => $fecha,
                    'clientesHoy' => 0,
                    'productosVendidos' => 0,
                    'ingresoDiario' => 0.00,
                ]);
                $cajasCreadas++;
                echo "Creada: {$caja->codigoCaja}\n";
            } else {
                echo "Ya existe: {$caja->codigoCaja}\n";
            }

            $ventasEnEsteDia = 0;
            $totalProductosDia = 0;
            $totalIngresosDia = 0;
            $totalClientesDia = 0;

            // ==================== 2. PROCESAR VENTAS DEL DÍA ====================
            foreach ($ventasDia as $ventaData) {
                $ventasCreadas++;
                $ventasEnEsteDia++;
                $totalClientesDia++;

                // Verificar cliente
                $cliente = Cliente::find($ventaData['cliente']);
                if (!$cliente) {
                    echo "   ❌ Cliente #{$ventaData['cliente']} no existe\n";
                    continue;
                }

                // Crear fecha completa
                $fechaCompleta = Carbon::parse($fecha . ' ' . $ventaData['hora']);

                // Obtener comprobante
                $comprobante = Comprobante::where('descripcionCOM', $ventaData['comprobante'])->first();

                if (!$comprobante) {
                    echo "   ❌ Comprobante '{$ventaData['comprobante']}' no existe\n";
                    continue;
                }

                // ========== 2.1 CREAR DETALLES PRIMERO (para calcular totales) ==========
                $subTotal = 0;
                $productosVendidosEnEstaVenta = 0;
                $detallesVenta = [];

                foreach ($ventaData['productos'] as $item) {
                    // Validar ID del producto (1-25)
                    if ($item['id'] < 1 || $item['id'] > 25) {
                        echo "   ❌ Producto #{$item['id']} no válido\n";
                        continue;
                    }

                    $producto = Producto::find($item['id']);

                    if (!$producto) {
                        echo "   ❌ Producto #{$item['id']} no existe\n";
                        continue;
                    }

                    if ($producto->stockP < $item['cant']) {
                        echo "   ⚠️  Producto #{$producto->id} sin stock suficiente (tiene {$producto->stockP}, necesita {$item['cant']})\n";
                        $item['cant'] = $producto->stockP;
                        if ($item['cant'] <= 0) continue;
                    }

                    $subtotal = $item['cant'] * $producto->precioP;
                    $subTotal += $subtotal;
                    $productosVendidosEnEstaVenta += $item['cant'];

                    // Guardar para crear después
                    $detallesVenta[] = [
                        'producto' => $producto,
                        'cantidad' => $item['cant'],
                        'subtotal' => $subtotal
                    ];
                }

                // Si no hay productos, saltar esta venta
                if ($subTotal == 0 || empty($detallesVenta)) {
                    $ventasCreadas--;
                    $ventasEnEsteDia--;
                    $totalClientesDia--;
                    continue;
                }

                // ========== 2.2 CALCULAR TOTALES DE LA VENTA ==========
                $IGV = $subTotal * 0.18;
                $montoTotal = $subTotal + $IGV;
                $totalIngresosDia += $montoTotal;
                $totalProductosDia += $productosVendidosEnEstaVenta;

                // ========== 2.3 CREAR VENTA CON TOTALES CORRECTOS ==========
                $venta = Venta::create([
                    'caja_id' => $caja->id,
                    'cliente_id' => $cliente->id,
                    'estado_transaccion_id' => $estadoPagado->id,
                    'subTotal' => round($subTotal, 2),
                    'IGV' => round($IGV, 2),
                    'montoTotal' => round($montoTotal, 2),
                    'created_at' => $fechaCompleta,
                    'updated_at' => $fechaCompleta,
                ]);

                // ========== 2.4 CREAR DETALLES DE LA VENTA ==========
                foreach ($detallesVenta as $detalleData) {
                    VentaDetalle::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $detalleData['producto']->id,
                        'cantidad' => $detalleData['cantidad'],
                        'precio_unitario' => $detalleData['producto']->precioP,
                        'subtotal' => $detalleData['subtotal'],
                        'created_at' => $fechaCompleta,
                        'updated_at' => $fechaCompleta,
                    ]);

                    // ACTUALIZAR STOCK DEL PRODUCTO
                    $detalleData['producto']->stockP -= $detalleData['cantidad'];
                    $detalleData['producto']->save();
                }

                // ========== 2.5 CREAR PAGO ==========
                Pago::create([
                    'venta_id' => $venta->id,
                    'importe' => $montoTotal,
                    'vuelto' => 0,
                    'comprobante_id' => $comprobante->id,
                    'created_at' => $fechaCompleta,
                    'updated_at' => $fechaCompleta,
                ]);

                // ========== 2.6 ACTUALIZAR CAJA MANUALMENTE (por si eventos fallan) ==========
                $caja->clientesHoy = $totalClientesDia;
                $caja->productosVendidos = $totalProductosDia;
                $caja->ingresoDiario = $totalIngresosDia;
                $caja->save();

                // Mostrar progreso
                echo "   ✓ Venta #{$ventasCreadas}: {$productosVendidosEnEstaVenta} productos, S/ " . number_format($montoTotal, 2) . "\n";
            }

            // ==================== 3. VERIFICAR TOTALES DE LA CAJA ====================
            $caja->refresh(); // Asegurar datos actualizados

            echo "   📊 RESUMEN DÍA:\n";
            echo "     • Ventas: {$ventasEnEsteDia}\n";
            echo "     • Clientes: {$caja->clientesHoy}\n";
            echo "     • Productos vendidos: {$caja->productosVendidos}\n";
            echo "     • Ingreso total: S/ " . number_format($caja->ingresoDiario, 2) . "\n";

            // Verificar consistencia
            if ($caja->clientesHoy != $ventasEnEsteDia) {
                echo "   ⚠️  ADVERTENCIA: Clientes ({$caja->clientesHoy}) ≠ Ventas ({$ventasEnEsteDia})\n";
            }

            // Guardar para resumen final
            $resumenCajas[$fecha] = [
                'codigo' => $caja->codigoCaja,
                'ventas' => $ventasEnEsteDia,
                'clientes' => $caja->clientesHoy,
                'productos' => $caja->productosVendidos,
                'ingreso' => $caja->ingresoDiario,
            ];

            if ($ventasCreadas >= 256) break;
        }

        // ==================== RESULTADOS FINALES ====================
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "✅ VENTASEEDER CON CAJAS COMPLETADO\n";
        echo str_repeat("=", 60) . "\n";

        echo "📈 ESTADÍSTICAS GENERALES:\n";
        echo "   • Cajas creadas: {$cajasCreadas} días\n";
        echo "   • Ventas totales: {$ventasCreadas}/256\n";

        $totalIngresos = array_sum(array_column($resumenCajas, 'ingreso'));
        $totalProductos = array_sum(array_column($resumenCajas, 'productos'));

        echo "   • Ingreso total octubre: S/ " . number_format($totalIngresos, 2) . "\n";
        echo "   • Productos totales vendidos: {$totalProductos}\n";

        // Mostrar resumen por días
        echo "\n📅 RESUMEN POR DÍA (Top 5 mayores ingresos):\n";
        uasort($resumenCajas, function ($a, $b) {
            return $b['ingreso'] <=> $a['ingreso'];
        });

        $count = 0;
        foreach ($resumenCajas as $fecha => $stats) {
            if ($count++ >= 5) break;
            echo "   {$fecha} [{$stats['codigo']}]: ";
            echo "S/ " . number_format($stats['ingreso'], 2);
            echo " ({$stats['ventas']} ventas, {$stats['productos']} productos)\n";
        }

        // Mostrar estado de stocks
        echo "\n📦 ESTADO DE STOCKS (31 Octubre):\n";
        $productos = Producto::orderBy('stockP')->get();

        $bajoStock = 0;
        foreach ($productos as $producto) {
            $icono = "✓";
            $alerta = "";

            if ($producto->stockP <= 5) {
                $icono = "🔥";
                $alerta = " - CRÍTICO";
                $bajoStock++;
            } elseif ($producto->stockP <= 10) {
                $icono = "⚠️⚠️";
                $alerta = " - Muy bajo";
                $bajoStock++;
            } elseif ($producto->stockP <= 15) {
                $icono = "⚠️";
                $alerta = " - Bajo stock";
                $bajoStock++;
            }

            echo "   {$icono} #{$producto->id} {$producto->descripcionP}: Stock {$producto->stockP}{$alerta}\n";
        }

        echo "\n🚨 PRODUCTOS CON STOCK ≤15: {$bajoStock}/25\n";
        echo "💡 Dashboard mostrará {$bajoStock} productos que necesitan atención\n";

        // Resumen de comprobantes
        echo "\n🧾 RESUMEN DE COMPROBANTES:\n";
        $boletas = Venta::whereHas('pago.comprobante', function ($query) {
            $query->where('descripcionCOM', 'Boleta');
        })->count();

        $facturas = Venta::whereHas('pago.comprobante', function ($query) {
            $query->where('descripcionCOM', 'Factura');
        })->count();

        echo "   • Boletas: {$boletas} ventas\n";
        echo "   • Facturas: {$facturas} ventas\n";
        echo "   • Total: " . ($boletas + $facturas) . " ventas\n";
    }
}
