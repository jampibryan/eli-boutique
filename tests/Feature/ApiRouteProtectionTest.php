<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiRouteProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function beforeRefreshingDatabase(): void
    {
        if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
            throw new \LogicException('Usar phpunit.analytics.xml: solo SQLite en memoria.');
        }
    }

    public function test_clientes_api_requires_sanctum_authentication(): void
    {
        $this->getJson('/api/clientes')
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_access_clientes_api(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('ver clientes', 'web'));
        Sanctum::actingAs($user, ['read']);

        $this->getJson('/api/clientes')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [],
            ]);
    }
}
