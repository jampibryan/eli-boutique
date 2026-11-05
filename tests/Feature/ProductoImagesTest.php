<?php

namespace Tests\Feature;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProductoImagesTest extends TestCase
{
    use RefreshDatabase;

    protected function beforeRefreshingDatabase(): void
    {
        if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
            throw new \LogicException('Estas pruebas requieren SQLite en memoria.');
        }
    }

    public function test_missing_photo_uses_an_inline_fallback_and_disables_the_error_handler(): void
    {
        $html = Blade::render('<x-producto-imagen :src="$src" alt="Prenda" class="producto-imagen" />', [
            'src' => '/img/productos/archivo-ausente.webp',
        ]);

        $this->assertStringContainsString('/img/productos/archivo-ausente.webp', $html);
        $this->assertStringContainsString('onerror="this.onerror=null;this.src=\'data:image/svg+xml;base64,', $html);
        $this->assertStringNotContainsString('Producto por defecto.webp', $html);
        $this->assertStringContainsString('class="producto-imagen"', $html);

        foreach ([null, '', '   '] as $src) {
            $empty = Blade::render('<x-producto-imagen :src="$src" />', compact('src'));
            $this->assertStringContainsString('src="data:image/svg+xml;base64,', $empty);
        }
    }

    public function test_catalogue_and_editor_render_and_a_photo_can_be_replaced(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('gestionar productos', 'web'));
        $this->actingAs($user);

        $category = DB::table('categoria_productos')->insertGetId(['nombreCP' => 'Prueba']);
        $gender = DB::table('producto_generos')->insertGetId(['descripcion' => 'Unisex']);
        $id = DB::table('productos')->insertGetId([
            'codigoP' => 'IMG-001', 'descripcionP' => 'Prenda de prueba',
            'categoria_producto_id' => $category, 'producto_genero_id' => $gender,
            'precioP' => 25, 'imagenP' => '/img/productos/archivo-ausente.webp',
        ]);

        $this->get('/productos')->assertOk()->assertSee('data:image/svg+xml;base64,', false);
        $this->get('/productos/'.$id.'/edit')->assertOk()
            ->assertSee('name="producto_genero_id"', false)->assertSee('Unisex');

        // Redirect uploads to an isolated public directory, never to the real photos.
        $originalPublicPath = public_path();
        $testPublicPath = storage_path('framework/testing/product-images-'.uniqid());
        mkdir($testPublicPath.'/img/productos', 0777, true);
        $this->app->usePublicPath($testPublicPath);

        try {
            $this->put('/productos/'.$id, [
                'codigoP' => 'IMG-001', 'descripcionP' => 'Prenda de prueba',
                'categoria_producto_id' => $category, 'producto_genero_id' => $gender,
                'precioP' => 25, 'imagenP' => UploadedFile::fake()->image('prenda.jpg'),
            ])->assertSessionHasNoErrors()->assertRedirect(route('productos.index'));

            $path = Producto::findOrFail($id)->imagenP;
            $this->assertStringStartsWith('/img/productos/', $path);
            $this->assertFileExists(public_path(ltrim($path, '/')));

            $this->put('/productos/'.$id, [
                'codigoP' => 'IMG-001', 'descripcionP' => 'Prenda de prueba',
                'categoria_producto_id' => $category, 'producto_genero_id' => $gender,
                'precioP' => 30,
            ])->assertSessionHasNoErrors()->assertRedirect(route('productos.index'));
            $this->assertSame($path, Producto::findOrFail($id)->imagenP);
        } finally {
            foreach (glob($testPublicPath.'/img/productos/*') as $file) {
                unlink($file);
            }
            rmdir($testPublicPath.'/img/productos');
            rmdir($testPublicPath.'/img');
            rmdir($testPublicPath);
            $this->app->usePublicPath($originalPublicPath);
        }
    }

    public function test_replacement_catalogue_covers_every_product_and_keeps_future_uploads(): void
    {
        $catalogue = config('product_images.catalogue');
        $this->assertCount(19, $catalogue);

        foreach ($catalogue as $code => $path) {
            $product = new Producto(['codigoP' => $code]);
            $this->assertSame($path, $product->imagen_url);
            $this->assertFileExists(public_path($path));
            $this->assertSame(IMAGETYPE_JPEG, getimagesize(public_path($path))[2]);
        }

        $product = new Producto([
            'codigoP' => 'CAM-002', 'imagenP' => '/img/productos/Camiseta básica algodón.webp',
        ]);
        $this->assertSame($catalogue['CAM-002'], $product->imagen_url);
        $product->imagenP = '/img/productos/nueva-foto.jpg';
        $this->assertSame('/img/productos/nueva-foto.jpg', $product->imagen_url);
        $this->assertNull((new Producto(['codigoP' => 'OTRO']))->imagen_url);
    }
}
