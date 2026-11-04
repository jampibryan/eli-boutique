<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SalesDetailAndCompanyTest extends TestCase
{
    use RefreshDatabase;

    protected function beforeRefreshingDatabase(): void
    {
        if(config('database.default')!=='sqlite' || config('database.connections.sqlite.database')!==':memory:') {
            throw new \LogicException('Estas pruebas requieren SQLite en memoria.');
        }
    }

    private function loginWith(string $permission): void
    {
        $user=User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate($permission,'web'));
        $this->actingAs($user);
    }

    private function sale(): array
    {
        $genre=DB::table('tipo_generos')->value('id');
        $client=DB::table('clientes')->insertGetId(['nombreCliente'=>'Cliente Histórico','apellidoCliente'=>'Prueba',
            'dniCliente'=>'12345678','tipo_genero_id'=>$genre,'deleted_at'=>now()]);
        $category=DB::table('categoria_productos')->insertGetId(['nombreCP'=>'Prueba']);
        $gender=DB::table('producto_generos')->insertGetId(['descripcion'=>'Unisex']);
        $product=DB::table('productos')->insertGetId(['codigoP'=>'P-01','descripcionP'=>'Prenda histórica',
            'categoria_producto_id'=>$category,'producto_genero_id'=>$gender,'precioP'=>999,'costo_compra'=>3,'deleted_at'=>now()]);
        $size=DB::table('producto_tallas')->insertGetId(['descripcion'=>'M']);
        $state=DB::table('estado_transacciones')->insertGetId(['descripcionET'=>'Pagado']);
        $receipt=DB::table('comprobantes')->insertGetId(['descripcionCOM'=>'Factura']);
        $sale=DB::table('ventas')->insertGetId(['codigoVenta'=>'0000001','cliente_id'=>$client,
            'estado_transaccion_id'=>$state,'subTotal'=>100,'IGV'=>18,'montoTotal'=>118,
            'ruc_factura'=>'20999000001','razon_social_factura'=>'Empresa congelada (Demo)',
            'direccion_factura'=>'Dirección histórica','created_at'=>'2026-10-15 12:00:00']);
        DB::table('venta_detalles')->insert(['venta_id'=>$sale,'producto_id'=>$product,'producto_talla_id'=>$size,
            'cantidad'=>2,'precio_unitario'=>59,'costo_unitario'=>3,'base_imponible'=>50,'igv'=>9,'subtotal'=>118]);
        DB::table('pagos')->insert(['venta_id'=>$sale,'comprobante_id'=>$receipt,'importe'=>150,'vuelto'=>32]);
        return [$sale,$client];
    }

    public function test_drawer_requires_a_web_session_and_sale_permission(): void
    {
        [$sale]=$this->sale();
        $this->getJson('/ventas/'.$sale)->assertUnauthorized();
        $this->actingAs(User::factory()->create())->getJson('/ventas/'.$sale)->assertForbidden();
    }

    public function test_drawer_returns_current_fields_and_deleted_historical_relations(): void
    {
        [$sale]=$this->sale();$this->loginWith('gestionar ventas');
        $this->getJson('/ventas/'.$sale)->assertOk()->assertJsonPath('id',$sale)
            ->assertJsonPath('cliente.nombreCliente','Cliente Histórico')
            ->assertJsonPath('detalles.0.producto.descripcionP','Prenda histórica')
            ->assertJsonPath('detalles.0.talla.descripcion','M')
            ->assertJsonPath('detalles.0.precio_unitario',59)
            ->assertJsonPath('montoTotal',118)->assertJsonPath('pago.comprobante.descripcionCOM','Factura');
        $this->getJson('/ventas/999999')->assertNotFound();
    }

    public function test_company_can_be_created_and_edited_without_a_fake_dni_or_personal_gender(): void
    {
        $this->loginWith('gestionar clientes');
        $data=['tipo_documento'=>'RUC','nombreCliente'=>'Empresa de prueba (Demo)','dniCliente'=>'20999000001',
            'direccionCliente'=>'Dirección de prueba'];
        $this->postJson('/clientes',$data)->assertSuccessful()->assertJsonPath('cliente.tipo_documento','RUC');
        $client=DB::table('clientes')->where('dniCliente',$data['dniCliente'])->first();
        $this->assertSame('',$client->apellidoCliente);
        $this->assertSame('No aplica',DB::table('tipo_generos')->where('id',$client->tipo_genero_id)->value('descripcionTG'));
        $this->put('/clientes/'.$client->id,$data+['apellidoCliente'=>'Ignorado'])->assertRedirect();
        $this->postJson('/clientes',$data)->assertUnprocessable()->assertJsonValidationErrors('dniCliente');
    }

    public function test_document_length_and_company_address_are_validated(): void
    {
        $this->loginWith('gestionar clientes');
        $this->postJson('/clientes',['tipo_documento'=>'RUC','nombreCliente'=>'Empresa','dniCliente'=>'12345678'])
            ->assertUnprocessable()->assertJsonValidationErrors(['dniCliente','direccionCliente']);
        $this->postJson('/clientes',['tipo_documento'=>'DNI','nombreCliente'=>'Persona','apellidoCliente'=>'Prueba',
            'dniCliente'=>'20999000001','tipo_genero_id'=>DB::table('tipo_generos')->value('id')])
            ->assertUnprocessable()->assertJsonValidationErrors('dniCliente');
    }

    public function test_invoice_templates_preserve_billing_and_price_snapshots(): void
    {
        [$sale,$client]=$this->sale();$this->loginWith('gestionar ventas');
        DB::table('clientes')->where('id',$client)->update(['nombreCliente'=>'Otro nombre actual']);
        $sale=\App\Models\Venta::with(['cliente','detalles.producto.tallas','detalles.talla','pago.comprobante'])->findOrFail($sale);
        foreach(['Venta.ticket','Venta.comprobante_moderno'] as $template) {
            $html=view($template,['venta'=>$sale,'tipoComprobante'=>'Factura'])->render();
            $this->assertStringContainsString('EMPRESA CONGELADA (DEMO)',$html);
            $this->assertStringContainsString('20999000001',$html);
            $this->assertStringContainsString('Dirección histórica',$html);
            $this->assertStringContainsString('118.00',$html);
            $this->assertStringNotContainsString('999.00',$html);
        }
    }
}
