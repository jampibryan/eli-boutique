<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\MobileReadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class MobileReadTest extends TestCase
{
    use RefreshDatabase;

    protected function beforeRefreshingDatabase(): void
    {
        if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
            throw new \LogicException('Pruebas móviles: solo SQLite en memoria con phpunit.analytics.xml.');
        }
    }

    private function reader(array $permissions): User
    {
        $user = User::factory()->create(['password' => Hash::make('prueba-segura')]);
        foreach ($permissions as $permission) { $user->givePermissionTo(Permission::findOrCreate($permission, 'web')); }
        return $user;
    }

    public function test_mobile_login_issues_read_only_token_and_logout_only_revokes_that_installation(): void
    {
        $user = $this->reader(['ver productos']);
        $other = $user->createToken('streamlit-preserved', ['read']);
        $result = $this->postJson('/api/mobile/v1/auth/login', ['email' => $user->email,
            'password' => 'prueba-segura', 'device_id' => str_repeat('a', 32)])
            ->assertOk()->assertJsonPath('data.user.modules', ['dashboard', 'productos']);
        $token = $result->json('data.token');
        $stored = $user->tokens()->where('name', 'like', 'mobile:%')->first();
        $this->assertSame(['read', 'mobile:read'], $stored->abilities);
        $this->assertNotNull($stored->expires_at);
        $this->withToken($token)->getJson('/api/mobile/v1/auth/me')->assertOk();
        $this->withToken($token)->postJson('/api/mobile/v1/auth/logout')->assertOk();
        $this->assertFalse($user->tokens()->whereKey($stored->id)->exists());
        $this->assertTrue($user->tokens()->whereKey($other->accessToken->id)->exists());
    }

    public function test_wrong_password_or_account_without_permissions_cannot_get_a_mobile_session(): void
    {
        $user = $this->reader([]);
        $body = ['email' => $user->email, 'password' => 'incorrecta', 'device_id' => str_repeat('b', 32)];
        $this->postJson('/api/mobile/v1/auth/login', $body)->assertUnprocessable();
        $body['password'] = 'prueba-segura';
        $this->postJson('/api/mobile/v1/auth/login', $body)->assertForbidden();
        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_two_factor_accounts_cannot_bypass_their_authenticator(): void
    {
        $user = $this->reader(['ver productos']);
        $user->forceFill(['two_factor_secret' => encrypt('secret-testing-only'), 'two_factor_confirmed_at' => now()])->save();
        $body = ['email' => $user->email, 'password' => 'prueba-segura', 'device_id' => str_repeat('c', 32)];
        $this->postJson('/api/mobile/v1/auth/login', $body)->assertUnprocessable()->assertJsonValidationErrors('two_factor_code');
        $provider = \Mockery::mock(\Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider::class);
        $provider->shouldReceive('verify')->once()->with('secret-testing-only', '123456')->andReturn(true);
        $this->app->instance(\Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider::class, $provider);
        $this->postJson('/api/mobile/v1/auth/login', $body + ['two_factor_code' => '123456'])->assertOk();
        $this->assertSame(1, $user->tokens()->count());
    }

    public function test_mobile_routes_require_token_scope_and_module_permission_and_reject_business_writes(): void
    {
        $this->getJson('/api/mobile/v1/productos')->assertUnauthorized();
        $user = $this->reader(['ver productos']);
        Sanctum::actingAs($user, ['read']);
        $this->withHeader('Authorization', 'Bearer testing')->getJson('/api/mobile/v1/productos')->assertForbidden();
        Sanctum::actingAs($user, ['read', 'mobile:read']);
        $this->getJson('/api/mobile/v1/productos')->assertOk();
        $this->getJson('/api/mobile/v1/compras')->assertForbidden();
        $this->getJson('/api/mobile/v1/graficos')->assertForbidden();
        $this->postJson('/api/mobile/v1/ventas', [])->assertStatus(405);
        $this->deleteJson('/api/mobile/v1/productos/1')->assertStatus(405);
        $this->getJson('/api/clientes')->assertForbidden();
    }

    public function test_mobile_pagination_is_bounded_and_detail_preserves_company_snapshot_and_size(): void
    {
        $user = $this->reader(['gestionar ventas']);
        Sanctum::actingAs($user, ['read', 'mobile:read']);
        $this->withHeader('Authorization', 'Bearer testing');
        $category = DB::table('categoria_productos')->insertGetId(['nombreCP' => 'Prueba']);
        $gender = DB::table('producto_generos')->insertGetId(['descripcion' => 'Unisex']);
        $product = DB::table('productos')->insertGetId(['codigoP' => 'P-1', 'descripcionP' => 'Histórico',
            'categoria_producto_id' => $category, 'producto_genero_id' => $gender, 'precioP' => 59, 'costo_compra' => 3, 'deleted_at' => now()]);
        $size = DB::table('producto_tallas')->insertGetId(['descripcion' => 'M']);
        $state = DB::table('estado_transacciones')->insertGetId(['descripcionET' => 'Pendiente']);
        foreach ([1, 2] as $code) {
            $sale = DB::table('ventas')->insertGetId(['codigoVenta' => '000'.$code, 'estado_transaccion_id' => $state, 'subTotal' => 100, 'IGV' => 18, 'montoTotal' => 118,
                'ruc_factura' => '20111111111', 'razon_social_factura' => 'Empresa histórica', 'direccion_factura' => 'Dirección congelada']);
            DB::table('venta_detalles')->insert(['venta_id' => $sale, 'producto_id' => $product, 'producto_talla_id' => $size,
                'cantidad' => 2, 'precio_unitario' => 59, 'base_imponible' => 50, 'igv' => 9, 'subtotal' => 118]);
        }
        $this->getJson('/api/mobile/v1/ventas?per_page=1')->assertOk()->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.total', 2)->assertJsonPath('meta.last_page', 2)->assertJsonMissingPath('data.0.detalles');
        $this->getJson('/api/mobile/v1/ventas/'.$sale)->assertOk()->assertJsonPath('data.razon_social_factura', 'Empresa histórica')
            ->assertJsonPath('data.detalles.0.producto.descripcionP', 'Histórico')->assertJsonPath('data.detalles.0.talla.descripcion', 'M')
            ->assertJsonPath('data.detalles.0.producto_id', $product);
        $this->getJson('/api/mobile/v1/ventas?per_page=1000')->assertUnprocessable();
    }

    public function test_charts_exclude_unpaid_sales_and_keep_payment_cash_and_current_stock_distinct(): void
    {
        $user = $this->reader(['ver reportes gráficos']);
        Sanctum::actingAs($user, ['read', 'mobile:read']);
        $this->withHeader('Authorization', 'Bearer testing');
        $state = DB::table('estado_transacciones')->insertGetId(['descripcionET' => 'Pagado']);
        $pending = DB::table('estado_transacciones')->insertGetId(['descripcionET' => 'Pendiente']);
        $receipt = DB::table('comprobantes')->insertGetId(['descripcionCOM' => 'Boleta']);
        foreach ([$state, $pending] as $status) {
            $sale = DB::table('ventas')->insertGetId(['codigoVenta' => 'V-'.$status, 'estado_transaccion_id' => $status,
                'subTotal' => 100, 'IGV' => 18, 'montoTotal' => 118, 'created_at' => '2026-10-15 12:00:00']);
            DB::table('pagos')->insert(['venta_id' => $sale, 'comprobante_id' => $receipt, 'importe' => 150, 'vuelto' => 32, 'created_at' => '2026-10-16 12:00:00']);
        }
        $response = $this->getJson('/api/mobile/v1/graficos?from=2026-10-15&to=2026-10-15')->assertOk();
        $response->assertJsonPath('data.ventas', 1)->assertJsonPath('data.monto_ventas', 118)
            ->assertJsonPath('data.ingresos', 0)->assertJsonPath('data.serie_ventas.0.fecha', '2026-10-15');
        $this->getJson('/api/mobile/v1/graficos?from=2026-10-15&to=2026-10-16')->assertOk()->assertJsonPath('data.ingresos', 118);
        $this->getJson('/api/mobile/v1/graficos?from=2020-01-01&to=2026-10-16')->assertUnprocessable();
        $this->assertSame(2, DB::table('ventas')->count());
    }
}
