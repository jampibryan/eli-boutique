<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Caja;
use App\Models\CategoriaProducto;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Comprobante;
use App\Models\EstadoTransaccion;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\ProductoGenero;
use App\Models\ProductoTalla;
use App\Models\ProductoTallaStock;
use App\Models\Proveedor;
use App\Models\TipoGenero;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Tests\TestCase;

class CompraPagoFlowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_receiving_an_approved_purchase_creates_or_updates_size_stock(): void
    {
        $context = $this->createCompraContext('Aprobada');
        $compra = $context['compra'];
        $producto = $context['producto'];
        $talla = $context['talla'];

        $response = $this->post(route('compras.recibir', $compra->id));

        $response->assertRedirect(route('compras.index'));
        $this->assertSame('Recibida', $compra->fresh()->estadoTransaccion->descripcionET);
        $this->assertDatabaseHas('producto_talla_stock', [
            'producto_id' => $producto->id,
            'producto_talla_id' => $talla->id,
            'stock' => 3,
        ]);
    }

    public function test_sale_payment_marks_sale_as_paid_and_creates_payment(): void
    {
        $context = $this->createVentaContext();
        $venta = $context['venta'];
        $comprobante = $context['comprobante'];
        $caja = $context['caja'];

        $response = $this->post(route('pagos.store', ['id' => $venta->id, 'type' => 'venta']), [
            'comprobante_id' => $comprobante->id,
            'importe' => 150.00,
        ]);

        $response->assertRedirect(route('ventas.index'));
        $this->assertSame('Pagado', $venta->fresh()->estadoTransaccion->descripcionET);
        $this->assertDatabaseHas('pagos', [
            'venta_id' => $venta->id,
            'comprobante_id' => $comprobante->id,
            'importe' => 150.00,
            'vuelto' => 50.00,
        ]);
        $this->assertSame('100.00', $caja->fresh()->ingresoDiario);
    }

    public function test_underpaid_sale_does_not_create_payment_or_change_status(): void
    {
        $context = $this->createVentaContext();
        $venta = $context['venta'];
        $comprobante = $context['comprobante'];

        $response = $this->from(route('pagos.create', ['id' => $venta->id, 'type' => 'venta']))
            ->post(route('pagos.store', ['id' => $venta->id, 'type' => 'venta']), [
                'comprobante_id' => $comprobante->id,
                'importe' => 90.00,
            ]);

        $response->assertRedirect(route('pagos.create', ['id' => $venta->id, 'type' => 'venta']));
        $response->assertSessionHasErrors(['importe']);
        $this->assertSame('Pendiente', $venta->fresh()->estadoTransaccion->descripcionET);
        $this->assertDatabaseMissing('pagos', [
            'venta_id' => $venta->id,
        ]);
    }

    public function test_purchase_payment_marks_purchase_as_paid_and_updates_cashbox_expense(): void
    {
        $context = $this->createCompraContext('Recibida');
        $compra = $context['compra'];
        $comprobante = $context['comprobante'];
        $caja = $context['caja'];

        $response = $this->post(route('pagos.store', ['id' => $compra->id, 'type' => 'compra']), [
            'comprobante_id' => $comprobante->id,
            'importe' => 250.00,
        ]);

        $response->assertRedirect(route('compras.index'));
        $this->assertSame('Pagada', $compra->fresh()->estadoTransaccion->descripcionET);
        $this->assertDatabaseHas('pagos', [
            'compra_id' => $compra->id,
            'comprobante_id' => $comprobante->id,
            'importe' => 250.00,
        ]);
        $this->assertSame('250.00', $caja->fresh()->egresoDiario);
    }

    private function createCompraContext(string $estadoDescripcion): array
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->withoutMiddleware(PermissionMiddleware::class);
        $this->actingAs(User::factory()->create());

        EstadoTransaccion::create(['descripcionET' => 'Borrador']);
        EstadoTransaccion::create(['descripcionET' => 'Enviada']);
        EstadoTransaccion::create(['descripcionET' => 'Cotizada']);
        EstadoTransaccion::create(['descripcionET' => 'Aprobada']);
        EstadoTransaccion::create(['descripcionET' => 'Recibida']);
        EstadoTransaccion::create(['descripcionET' => 'Pagada']);

        $estado = EstadoTransaccion::where('descripcionET', $estadoDescripcion)->firstOrFail();
        $comprobante = Comprobante::create(['descripcionCOM' => 'Factura']);

        $caja = Caja::create([
            'fecha' => today(),
            'hora_cierre' => null,
            'clientesHoy' => 0,
            'productosVendidos' => 0,
            'ingresoDiario' => 0,
            'egresoDiario' => 0,
        ]);

        $proveedor = Proveedor::create([
            'nombreEmpresa' => 'Proveedor SAC',
            'nombreProveedor' => 'Maria',
            'apellidoProveedor' => 'Lopez',
            'RUC' => '20123456789',
            'direccionProveedor' => 'Av. Lima 123',
            'correoProveedor' => 'proveedor@example.com',
            'telefonoProveedor' => '999111222',
        ]);

        $categoria = CategoriaProducto::create([
            'nombreCP' => 'Polos',
            'descripcionCP' => 'Categoria test',
        ]);

        $productoGenero = ProductoGenero::create([
            'descripcion' => 'Unisex',
        ]);

        $producto = Producto::create([
            'codigoP' => 'CP001',
            'categoria_producto_id' => $categoria->id,
            'producto_genero_id' => $productoGenero->id,
            'descripcionP' => 'Producto compra',
            'precioP' => 80.00,
        ]);

        $talla = ProductoTalla::create([
            'descripcion' => 'M',
        ]);

        $compra = Compra::create([
            'proveedor_id' => $proveedor->id,
            'estado_transaccion_id' => $estado->id,
            'subtotal' => 211.86,
            'igv' => 38.14,
            'total' => 250.00,
        ]);

        CompraDetalle::create([
            'compra_id' => $compra->id,
            'producto_id' => $producto->id,
            'producto_talla_id' => $talla->id,
            'cantidad' => 3,
            'precio_cotizado' => 70.00,
            'precio_final' => 70.00,
            'subtotal_linea' => 210.00,
        ]);

        return [
            'caja' => $caja,
            'compra' => $compra,
            'producto' => $producto,
            'talla' => $talla,
            'comprobante' => $comprobante,
        ];
    }

    private function createVentaContext(): array
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->withoutMiddleware(PermissionMiddleware::class);
        $this->actingAs(User::factory()->create());

        $estadoPendiente = EstadoTransaccion::create(['descripcionET' => 'Pendiente']);
        EstadoTransaccion::create(['descripcionET' => 'Pagado']);
        EstadoTransaccion::create(['descripcionET' => 'Anulado']);
        $comprobante = Comprobante::create(['descripcionCOM' => 'Boleta']);

        $caja = Caja::create([
            'fecha' => today(),
            'hora_cierre' => null,
            'clientesHoy' => 0,
            'productosVendidos' => 0,
            'ingresoDiario' => 0,
            'egresoDiario' => 0,
        ]);

        $tipoGenero = TipoGenero::create([
            'descripcionTG' => 'Masculino',
        ]);

        $cliente = Cliente::create([
            'nombreCliente' => 'Carlos',
            'apellidoCliente' => 'Perez',
            'dniCliente' => '12345670',
            'correoCliente' => 'cliente.pago@example.com',
            'telefonoCliente' => '999333444',
            'tipo_genero_id' => $tipoGenero->id,
        ]);

        $venta = Venta::create([
            'caja_id' => $caja->id,
            'cliente_id' => $cliente->id,
            'estado_transaccion_id' => $estadoPendiente->id,
            'subTotal' => 84.75,
            'IGV' => 15.25,
            'montoTotal' => 100.00,
        ]);

        return [
            'caja' => $caja,
            'venta' => $venta,
            'comprobante' => $comprobante,
        ];
    }
}
