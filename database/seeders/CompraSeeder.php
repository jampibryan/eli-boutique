<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\EstadoTransaccion;
use App\Models\Comprobante;
use App\Models\Pago;
use Carbon\Carbon;

class CompraSeeder extends Seeder
{
    public function run()
    {
        // Fechas de las compras (semanales)
        // $fechasCompras = [
        //     '2024-09-08', '2024-09-15', '2024-09-22', '2024-09-29', 
        //     '2024-10-06', '2024-10-13', '2024-10-20', '2024-10-27', 
        //     '2024-11-03', '2024-11-10',
        // ];

        $fechasCompras = [
            '2024-08-11', '2024-09-18', '2024-09-25', '2024-09-01', 
            '2024-09-08', '2024-09-15', '2024-09-22', '2024-09-29', 
            '2024-10-06', '2024-10-13', '2024-10-20', '2024-10-27', 
            '2024-11-03',
        ];

        // Crear compras para cada una de las fechas
        foreach ($fechasCompras as $fecha) {
            $this->realizarCompra($fecha);
        }
    }

    // Método auxiliar para realizar una compra
    private function realizarCompra($fecha)
    {
        // Obtener un proveedor aleatorio
        $proveedor = Proveedor::inRandomOrder()->first();

        // Obtener el estado "Pendiente" para la compra
        $estadoPendiente = EstadoTransaccion::where('descripcionET', 'Pendiente')->first();

        // Crear la compra (Estado: Pendiente)
        $compra = Compra::create([
            'proveedor_id' => $proveedor->id,
            'estado_transaccion_id' => $estadoPendiente->id,
            'codigoCompra' => $this->generarCodigoCompra(),
            'created_at' => $fecha,
            'updated_at' => $fecha,
        ]);

        // Seleccionar productos aleatorios para la compra (por ejemplo, entre 3 y 5 productos)
        $productos = Producto::inRandomOrder()->take(rand(4, 8))->get();

        foreach ($productos as $producto) {
            // Determinar una cantidad aleatoria para cada producto entre 50 y 100
            $cantidad = rand(15, 30);  // Definimos una cantidad aleatoria entre 50 y 100 para cada compra

            // Registrar el detalle de la compra
            CompraDetalle::create([
                'compra_id' => $compra->id,
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
            ]);

            // Actualizar el stock del producto
            $producto->stockP += $cantidad;  // Aumentar el stock del producto
            $producto->save();
        }

        // Realizar el pago de la compra (importe será igual al monto total calculado)
        $this->realizarPago($compra);

        // Cambiar el estado de la compra a "Pagado"
        $estadoPagado = EstadoTransaccion::where('descripcionET', 'Pagado')->first();
        $compra->estado_transaccion_id = $estadoPagado->id;
        $compra->save();

        // Simular el "Recibir pedido" y cambiar el estado de la compra a "Recibido"
        $this->recibirPedido($compra);
    }

    // Método para realizar el pago de la compra
    private function realizarPago($compra)
    {
        // Calcular el total de la compra (sumando la cantidad de productos * su precio con descuento)
        $totalCompra = 0;
        foreach ($compra->detalles as $detalle) {
            $producto = $detalle->producto;
            
            // Reducir el precio del producto en un 60% (solo para cálculo del importe)
            $precioConDescuento = $producto->precioP * 0.40;  // Reducir un 60% del precio original

            // Calcular el total para ese producto (cantidad * precio con descuento)
            $totalCompra += $detalle->cantidad * $precioConDescuento;
        }

        // Realizar el pago (monto igual al total de la compra)
        $importe = $totalCompra;
        $vuelto = 0; // El vuelto será 0 ya que el pago es exacto

        // Seleccionar un comprobante aleatorio (Boleta o Factura)
        $comprobante = Comprobante::inRandomOrder()->first();

        // Crear el pago asociado a la compra
        $pago = Pago::create([
            'compra_id' => $compra->id,
            'importe' => $importe,
            'vuelto' => $vuelto,
            'comprobante_id' => $comprobante->id,
        ]);
    }

    // Método para simular la recepción del pedido y cambiar el estado a "Recibido"
    private function recibirPedido($compra)
    {
        // Cambiar el estado de la compra a "Recibido"
        $estadoRecibido = EstadoTransaccion::where('descripcionET', 'Recibido')->first();
        if ($estadoRecibido) {
            $compra->estado_transaccion_id = $estadoRecibido->id;
            $compra->save();
        }
    }

    // Generar código de compra
    private function generarCodigoCompra()
    {
        $ultimoCodigo = Compra::max('codigoCompra');
        return str_pad((int)$ultimoCodigo + 1, 7, '0', STR_PAD_LEFT);
    }
}
