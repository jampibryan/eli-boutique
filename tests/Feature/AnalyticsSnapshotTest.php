<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AnalyticsSnapshotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AnalyticsSnapshotTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/analitica/v1/snapshot';

    protected function beforeRefreshingDatabase(): void
    {
        // RefreshDatabase can drop tables: guard BEFORE the trait runs migrations.
        if (config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:') {
            throw new \LogicException('Ejecutar con phpunit.analytics.xml: solo SQLite en memoria.');
        }
    }

    public function test_snapshot_requires_authentication(): void
    {
        $this->getJson(self::ENDPOINT)->assertUnauthorized();
    }

    public function test_snapshot_requires_report_permission_and_read_ability(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['read']);
        $this->getJson(self::ENDPOINT)->assertForbidden();

        $user->givePermissionTo(Permission::findOrCreate('ver reportes gráficos', 'web'));
        Sanctum::actingAs($user, ['update']);
        $this->getJson(self::ENDPOINT)->assertForbidden();
    }

    public function test_empty_snapshot_is_a_valid_contract(): void
    {
        $this->authorizeReader();
        $this->getJson(self::ENDPOINT)->assertOk()
            ->assertJsonPath('schema_version', '1.0')
            ->assertJsonPath('data.ventas', [])
            ->assertJsonPath('data.productos', [])
            ->assertJsonStructure(['revision', 'generated_at', 'data' => ['compras', 'pagos', 'cajas']]);
    }

    public function test_sales_exclude_unpaid_and_cancelled_but_preserve_deleted_products(): void
    {
        $this->authorizeReader();
        $category = DB::table('categoria_productos')->insertGetId(['nombreCP' => 'Prueba']);
        $gender = DB::table('producto_generos')->insertGetId(['descripcion' => 'Unisex']);
        $product = DB::table('productos')->insertGetId([
            'codigoP' => 'P-TEST', 'descripcionP' => 'Producto historico',
            'categoria_producto_id' => $category, 'producto_genero_id' => $gender,
            'precioP' => 99, 'costo_compra' => 20, 'deleted_at' => now(),
        ]);
        $size = DB::table('producto_tallas')->insertGetId(['descripcion' => 'M']);
        DB::table('producto_talla_stock')->insert([
            'producto_id' => $product, 'producto_talla_id' => $size, 'stock' => 7,
        ]);
        $receipt = DB::table('comprobantes')->insertGetId(['descripcionCOM' => 'Boleta']);

        foreach (['Pagado', 'Pendiente', 'Anulado', 'Pagado'] as $index => $stateName) {
            $state = DB::table('estado_transacciones')->where('descripcionET', $stateName)->value('id')
                ?? DB::table('estado_transacciones')->insertGetId(['descripcionET' => $stateName]);
            $sale = DB::table('ventas')->insertGetId([
                'codigoVenta' => (string) ($index + 1), 'estado_transaccion_id' => $state,
                'subTotal' => 100, 'IGV' => 18, 'montoTotal' => 118,
                'ruc_factura' => '20123456789', 'razon_social_factura' => 'Dato privado',
                'created_at' => '2026-10-15 12:00:00',
            ]);
            DB::table('venta_detalles')->insert([
                'venta_id' => $sale, 'producto_id' => $product, 'producto_talla_id' => $size,
                'cantidad' => 2, 'precio_unitario' => 59, 'costo_unitario' => 20,
                'base_imponible' => 50, 'igv' => 9, 'subtotal' => 118,
            ]);
            if ($index !== 3) {
                DB::table('pagos')->insert([
                    'venta_id' => $sale, 'comprobante_id' => $receipt, 'importe' => 150,
                    'vuelto' => 32, 'created_at' => '2026-10-15 12:01:00',
                ]);
            }
        }

        $before = DB::table('producto_talla_stock')->value('stock');
        $response = $this->getJson(self::ENDPOINT)->assertOk();
        $response->assertJsonCount(1, 'data.ventas')
            ->assertJsonPath('data.ventas.0.precio_unitario', 59)
            ->assertJsonPath('data.ventas.0.cantidad_vendida', 2)
            ->assertJsonPath('data.productos.0.activo', false)
            ->assertJsonPath('data.productos.0.stock_total', 7)
            ->assertJsonPath('data.pagos.0.monto_neto', 118)
            ->assertDontSee('Dato privado');
        $this->assertSame($before, DB::table('producto_talla_stock')->value('stock'));

        $revision = $response->json('revision');
        $this->assertSame($revision, app(AnalyticsSnapshotService::class)->snapshot()['revision']);
        DB::table('producto_talla_stock')->update(['stock' => 8]);
        $this->assertNotSame($revision, app(AnalyticsSnapshotService::class)->snapshot()['revision']);
    }

    private function authorizeReader(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('ver reportes gráficos', 'web'));
        Sanctum::actingAs($user, ['read']);
    }

    public function test_demo_cashboxes_are_identified_without_changing_original_records(): void
    {
        $this->authorizeReader();
        $cash=DB::table('cajas')->insertGetId(['codigoCaja'=>'123456','fecha'=>'2026-10-15']);
        DB::table('demo_data_batches')->insert(['batch_id'=>'test-demo','plan_hash'=>str_repeat('a',64),
            'baseline_fingerprint'=>str_repeat('b',64),'status'=>'applied','created_at'=>now(),
            'summary_json'=>json_encode(['desde'=>'2026-06-01','hasta'=>'2026-10-15','counts'=>['cajas'=>1]])]);
        DB::table('demo_data_records')->insert(['batch_id'=>'test-demo','table_name'=>'cajas','record_id'=>$cash,
            'action'=>'insert','after_json'=>'{}']);
        $this->getJson(self::ENDPOINT)->assertOk()->assertJsonPath('provenance.has_demo_data',true)
            ->assertJsonPath('data.cajas.0.es_demostracion',true);
    }
}
