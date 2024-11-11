<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
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
    public function run()
    {
        $faker = Faker::create();

        // Fechas de inicio y fin para los tres meses
        $agosto = Carbon::create(2024, 8, 1);  // 1 de agosto de 2024
        $septiembre = Carbon::create(2024, 9, 1); // 1 de septiembre de 2024
        $octubre = Carbon::create(2024, 10, 1);   // 1 de octubre de 2024
        $noviembre = Carbon::create(2024, 11, 1);  // 1 de noviembre de 2024

        // Generar ventas para agosto (4 ventas por día)
        for ($day = 1; $day <= 31; $day++) {
            $fecha = $agosto->copy()->day($day); // Establecer la fecha de venta en agosto
            foreach (range(1, 4) as $index) {
                $this->crearVentaConPago($fecha);
            }
        }

        // Generar ventas para septiembre (4 ventas por día)
        for ($day = 1; $day <= 30; $day++) {
            $fecha = $septiembre->copy()->day($day); // Establecer la fecha de venta en septiembre
            foreach (range(1, 4) as $index) {
                $this->crearVentaConPago($fecha);
            }
        }

        // Generar ventas para octubre (4 ventas por día)
        for ($day = 1; $day <= 31; $day++) {
            $fecha = $octubre->copy()->day($day); // Establecer la fecha de venta en octubre
            foreach (range(1, 4) as $index) {
                $this->crearVentaConPago($fecha);
            }
        }

        // Generar ventas para noviembre (4 ventas por día hasta el 10)
        for ($day = 1; $day <= 10; $day++) {
            $fecha = $noviembre->copy()->day($day); // Establecer la fecha de venta en noviembre
            foreach (range(1, 4) as $index) {
                $this->crearVentaConPago($fecha);
            }
        }
    }

    // Método auxiliar para crear una venta con pago
    private function crearVentaConPago($fecha)
    {
        // Obtener un cliente aleatorio
        $cliente = Cliente::inRandomOrder()->first();

        // Obtener el estado "Pendiente"
        $estadoPendiente = EstadoTransaccion::where('descripcionET', 'Pendiente')->first();

        // Crear una nueva venta
        $venta = Venta::create([
            'cliente_id' => $cliente->id,
            'estado_transaccion_id' => $estadoPendiente->id,
            'subTotal' => 0,  // Inicialmente el subtotal es 0
            'IGV' => 0,       // Inicialmente el IGV es 0
            'montoTotal' => 0, // Inicialmente el monto total es 0
            'created_at' => $fecha,  // Asignar la fecha de la venta
            'updated_at' => $fecha,  // Asignar la fecha de actualización
        ]);

        // Seleccionar entre 1 y 2 productos aleatorios
        $productos = Producto::inRandomOrder()->take(rand(1, 2))->get();

        $subTotal = 0;

        foreach ($productos as $producto) {
            // Calcular el subtotal de cada producto (cantidad * precio)
            $cantidad = rand(1, 2); // Cantidad aleatoria de productos (1 a 2)
            $subtotal = $cantidad * $producto->precioP;
            $subTotal += $subtotal;

            // Crear un detalle de venta para cada producto
            VentaDetalle::create([
                'venta_id' => $venta->id,
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $producto->precioP,
                'subtotal' => $subtotal,
            ]);

            // Actualizar el stock del producto
            $producto->stockP -= $cantidad;
            $producto->save();
        }

        // Calcular el IGV (por ejemplo, 18%)
        $IGV = $subTotal * 0.18;

        // Calcular el monto total
        $montoTotal = $subTotal + $IGV;

        // Actualizar la venta con el subtotal, IGV y monto total
        $venta->subTotal = $subTotal;
        $venta->IGV = $IGV;
        $venta->montoTotal = $montoTotal;
        $venta->save();

        // Crear el pago (importe será igual al monto total para que el vuelto sea 0)
        $importe = $montoTotal;
        $vuelto = 0; // El vuelto será 0 ya que el pago es exacto

        // Seleccionar un comprobante aleatorio (Boleta o Factura)
        $comprobante = Comprobante::inRandomOrder()->first();

        // Crear el pago
        $pago = Pago::create([
            'venta_id' => $venta->id,
            'importe' => $importe,
            'vuelto' => $vuelto,
            'comprobante_id' => $comprobante->id,
        ]);

        // Actualizar el estado de la venta a "Pagado"
        $estadoPagado = EstadoTransaccion::where('descripcionET', 'Pagado')->first();
        $venta->estado_transaccion_id = $estadoPagado->id;
        $venta->save();
    }
}
