<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiRouteProtectionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_clientes_api_requires_sanctum_authentication(): void
    {
        $this->getJson('/api/clientes')
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_access_clientes_api(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/clientes')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [],
            ]);
    }
}
