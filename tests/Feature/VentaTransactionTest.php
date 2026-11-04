<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Caja;
use App\Models\CategoriaProducto;
use App\Models\Cliente;
use App\Models\EstadoTransaccion;
use App\Models\Producto;
use App\Models\ProductoGenero;
use App\Models\ProductoTalla;
use App\Models\ProductoTallaStock;
use App\Models\TipoGenero;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Tests\TestCase;

class VentaTransactionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_sale_is_not_created_when_size_stock_is_insufficient(): void
    {
        $initialVentasCount = Venta::count();
        $initialDetallesCount = VentaDetalle::count();

        $context = $this->createVentaContext(1);
        $cliente = $context['cliente'];
        $producto = $context['producto'];
        $talla = $context['talla'];
        $stock = $context['stock'];

        $response = $this->from(route('ventas.create'))->post(route('ventas.store'), [
            'cliente_id' => $cliente->id,
            'montoTotal' => 119.80,
            'productos' => [
                [
                    'id' => $producto->id,
                    'talla_id' => $talla->id,
                    'cantidad' => 2,
                ],
            ],
        ]);

        $response->assertRedirect(route('ventas.create'));
        $response->assertSessionHasErrors(['productos']);

        $this->assertSame(1, $stock->fresh()->stock);
        $this->assertSame($initialVentasCount, Venta::count());
        $this->assertSame($initialDetallesCount, VentaDetalle::count());
    }

    public function test_sale_detail_persists_the_selected_size_and_returns_stock_on_cancel(): void
    {
        $context = $this->createVentaContext(5);
        $cliente = $context['cliente'];
        $producto = $context['producto'];
        $talla = $context['talla'];
        $stock = $context['stock'];
        $estadoAnulado = $context['estado_anulado'];

        $response = $this->post(route('ventas.store'), [
            'cliente_id' => $cliente->id,
            'montoTotal' => 119.80,
            'productos' => [
                [
                    'id' => $producto->id,
                    'talla_id' => $talla->id,
                    'cantidad' => 2,
                ],
            ],
        ]);

        $venta = Venta::with('detalles')->latest('id')->firstOrFail();
        $detalle = $venta->detalles->first();

        $response->assertRedirect(route('pagos.create', ['id' => $venta->id, 'type' => 'venta']));
        $this->assertDatabaseHas('venta_detalles', [
            'id' => $detalle->id,
            'producto_id' => $producto->id,
            'producto_talla_id' => $talla->id,
            'cantidad' => 2,
        ]);
        $this->assertSame(3, $stock->fresh()->stock);

        $venta->anular();

        $this->assertSame((int) $estadoAnulado->id, (int) $venta->fresh()->estado_transaccion_id);
        $this->assertSame(5, $stock->fresh()->stock);
    }

    public function test_sale_update_uses_size_stock_instead_of_general_stock(): void
    {
        $context = $this->createVentaContext(5);
        $cliente = $context['cliente'];
        $producto = $context['producto'];
        $talla = $context['talla'];
        $stock = $context['stock'];

        $this->post(route('ventas.store'), [
            'cliente_id' => $cliente->id,
            'montoTotal' => 119.80,
            'productos' => [
                [
                    'id' => $producto->id,
                    'talla_id' => $talla->id,
                    'cantidad' => 2,
                ],
            ],
        ]);

        $venta = Venta::latest('id')->firstOrFail();

        $response = $this->put(route('ventas.update', $venta), [
            'cliente_id' => $cliente->id,
            'montoTotal' => 239.60,
            'productos' => [
                [
                    'id' => $producto->id,
                    'talla_id' => $talla->id,
                    'cantidad' => 4,
                ],
            ],
        ]);

        $response->assertRedirect(route('pagos.create', ['id' => $venta->id, 'type' => 'venta']));
        $this->assertDatabaseHas('venta_detalles', [
            'venta_id' => $venta->id,
            'producto_id' => $producto->id,
            'producto_talla_id' => $talla->id,
            'cantidad' => 4,
        ]);
        $this->assertSame(1, $stock->fresh()->stock);
    }

    public function test_sale_update_distinguishes_the_same_product_in_multiple_sizes(): void
    {
        $context = $this->createVentaContext(5);
        $cliente = $context['cliente'];
        $producto = $context['producto'];
        $tallaM = $context['talla'];
        $stockM = $context['stock'];
        $tallaL = ProductoTalla::create([
            'descripcion' => 'L',
        ]);
        $stockL = ProductoTallaStock::create([
            'producto_id' => $producto->id,
            'producto_talla_id' => $tallaL->id,
            'stock' => 5,
        ]);

        $this->post(route('ventas.store'), [
            'cliente_id' => $cliente->id,
            'montoTotal' => 119.80,
            'productos' => [
                [
                    'id' => $producto->id,
                    'talla_id' => $tallaM->id,
                    'cantidad' => 1,
                ],
                [
                    'id' => $producto->id,
                    'talla_id' => $tallaL->id,
                    'cantidad' => 1,
                ],
            ],
        ]);

        $venta = Venta::with('detalles')->latest('id')->firstOrFail();

        $response = $this->put(route('ventas.update', $venta), [
            'cliente_id' => $cliente->id,
            'montoTotal' => 179.70,
            'productos' => [
                [
                    'id' => $producto->id,
                    'talla_id' => $tallaM->id,
                    'cantidad' => 2,
                ],
                [
                    'id' => $producto->id,
                    'talla_id' => $tallaL->id,
                    'cantidad' => 1,
                ],
            ],
        ]);

        $response->assertRedirect(route('pagos.create', ['id' => $venta->id, 'type' => 'venta']));
        $this->assertDatabaseHas('venta_detalles', [
            'venta_id' => $venta->id,
            'producto_id' => $producto->id,
            'producto_talla_id' => $tallaM->id,
            'cantidad' => 2,
        ]);
        $this->assertDatabaseHas('venta_detalles', [
            'venta_id' => $venta->id,
            'producto_id' => $producto->id,
            'producto_talla_id' => $tallaL->id,
            'cantidad' => 1,
        ]);
        $this->assertSame(3, $stockM->fresh()->stock);
        $this->assertSame(4, $stockL->fresh()->stock);
    }

    public function test_cancelling_pending_sale_does_not_decrement_cashbox_daily_income(): void
    {
        $context = $this->createVentaContext(5);
        $cliente = $context['cliente'];
        $producto = $context['producto'];
        $talla = $context['talla'];
        $caja = $context['caja'];

        $this->post(route('ventas.store'), [
            'cliente_id' => $cliente->id,
            'montoTotal' => 119.80,
            'productos' => [
                [
                    'id' => $producto->id,
                    'talla_id' => $talla->id,
                    'cantidad' => 2,
                ],
            ],
        ]);

        $venta = Venta::latest('id')->firstOrFail();
        $this->assertSame('0.00', $caja->fresh()->ingresoDiario);

        $response = $this->post(route('ventas.anular', $venta->id));
        $response->assertRedirect(route('ventas.index'));

        // El ingresoDiario de la caja no debe volverse negativo ni decrementarse
        $this->assertSame('0.00', $caja->fresh()->ingresoDiario);
        $this->assertSame(0, $caja->fresh()->clientesHoy);
        $this->assertSame(0, $caja->fresh()->productosVendidos);
    }

    public function test_sales_and_details_still_resolve_when_product_or_client_is_soft_deleted(): void
    {
        $context = $this->createVentaContext(5);
        $cliente = $context['cliente'];
        $producto = $context['producto'];
        $talla = $context['talla'];

        $this->post(route('ventas.store'), [
            'cliente_id' => $cliente->id,
            'montoTotal' => 119.80,
            'productos' => [
                [
                    'id' => $producto->id,
                    'talla_id' => $talla->id,
                    'cantidad' => 2,
                ],
            ],
        ]);

        $venta = Venta::with(['cliente', 'detalles.producto'])->latest('id')->firstOrFail();

        // Eliminación lógica de cliente y producto
        $producto->delete();
        $cliente->delete();

        // Recargar venta con relaciones
        $ventaFresca = Venta::with(['cliente', 'detalles.producto'])->find($venta->id);

        $this->assertNotNull($ventaFresca->cliente);
        $this->assertSame('Juan', $ventaFresca->cliente->nombreCliente);
        $this->assertNotNull($ventaFresca->detalles->first()->producto);
        $this->assertSame('Polo basico', $ventaFresca->detalles->first()->producto->descripcionP);
    }

    private function createVentaContext(int $stockInicial): array
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->withoutMiddleware(PermissionMiddleware::class);
        $this->actingAs(User::factory()->create());

        $estadoPendiente = EstadoTransaccion::firstOrCreate(['descripcionET' => 'Pendiente']);
        EstadoTransaccion::firstOrCreate(['descripcionET' => 'Pagado']);
        $estadoAnulado = EstadoTransaccion::firstOrCreate(['descripcionET' => 'Anulado']);

        $caja = Caja::whereDate('fecha', today())->first() ?? Caja::create([
            'fecha' => today(),
            'hora_cierre' => null,
            'clientesHoy' => 0,
            'productosVendidos' => 0,
            'ingresoDiario' => 0,
            'egresoDiario' => 0,
        ]);

        $tipoGenero = TipoGenero::firstOrCreate([
            'descripcionTG' => 'Masculino',
        ]);

        $cliente = Cliente::create([
            'nombreCliente' => 'Juan',
            'apellidoCliente' => 'Perez',
            'dniCliente' => '12345678',
            'correoCliente' => 'juan@example.com',
            'telefonoCliente' => '999888777',
            'tipo_genero_id' => $tipoGenero->id,
        ]);

        $categoria = CategoriaProducto::firstOrCreate([
            'nombreCP' => 'Polos',
            'descripcionCP' => 'Ropa casual',
        ]);

        $productoGenero = ProductoGenero::firstOrCreate([
            'descripcion' => 'Unisex',
        ]);

        $producto = Producto::create([
            'codigoP' => 'P001',
            'categoria_producto_id' => $categoria->id,
            'producto_genero_id' => $productoGenero->id,
            'descripcionP' => 'Polo basico',
            'precioP' => 59.90,
        ]);

        $talla = ProductoTalla::firstOrCreate([
            'descripcion' => 'M',
        ]);

        $stock = ProductoTallaStock::create([
            'producto_id' => $producto->id,
            'producto_talla_id' => $talla->id,
            'stock' => $stockInicial,
        ]);

        return [
            'cliente' => $cliente,
            'producto' => $producto,
            'talla' => $talla,
            'stock' => $stock,
            'caja' => $caja,
            'estado_pendiente' => $estadoPendiente,
            'estado_anulado' => $estadoAnulado,
        ];
    }
}
