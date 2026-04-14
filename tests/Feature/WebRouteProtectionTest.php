<?php

namespace Tests\Feature;

use Tests\TestCase;

class WebRouteProtectionTest extends TestCase
{
    public function test_guest_is_redirected_from_prediccion_route(): void
    {
        $this->get('/prediccion')
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_pago_creation_route(): void
    {
        $this->get('/pagos/create/1/venta')
            ->assertRedirect(route('login'));
    }
}
