<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * La raíz de la aplicación redirige al listado de citas (MediCitas).
     */
    public function test_la_raiz_redirige_al_listado_de_citas(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/citas');
    }
}
