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

        $this->assertDatabaseCount('ventas', 0);
        $this->assertDatabaseCount('venta_detalles', 0);
        $this->assertSame(1, $stock->fresh()->stock);
        $this->assertSame(0, Venta::count());
        $this->assertSame(0, VentaDetalle::count());
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

        $venta = Venta::with('detalles')->firstOrFail();
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

        $this->assertSame($estadoAnulado->id, $venta->fresh()->estado_transaccion_id);
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

        $venta = Venta::firstOrFail();

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

        $venta = Venta::with('detalles')->firstOrFail();

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
        $this->assertSame(2, $venta->fresh()->detalles()->count());
    }

    private function createVentaContext(int $stockInicial): array
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->withoutMiddleware(PermissionMiddleware::class);
        $this->actingAs(User::factory()->create());

        $estadoPendiente = EstadoTransaccion::create(['descripcionET' => 'Pendiente']);
        EstadoTransaccion::create(['descripcionET' => 'Pagado']);
        $estadoAnulado = EstadoTransaccion::create(['descripcionET' => 'Anulado']);

        Caja::create([
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
            'nombreCliente' => 'Juan',
            'apellidoCliente' => 'Perez',
            'dniCliente' => '12345678',
            'correoCliente' => 'juan@example.com',
            'telefonoCliente' => '999888777',
            'tipo_genero_id' => $tipoGenero->id,
        ]);

        $categoria = CategoriaProducto::create([
            'nombreCP' => 'Polos',
            'descripcionCP' => 'Ropa casual',
        ]);

        $productoGenero = ProductoGenero::create([
            'descripcion' => 'Unisex',
        ]);

        $producto = Producto::create([
            'codigoP' => 'P001',
            'categoria_producto_id' => $categoria->id,
            'producto_genero_id' => $productoGenero->id,
            'descripcionP' => 'Polo basico',
            'precioP' => 59.90,
        ]);

        $talla = ProductoTalla::create([
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
            'estado_pendiente' => $estadoPendiente,
            'estado_anulado' => $estadoAnulado,
        ];
    }
}
