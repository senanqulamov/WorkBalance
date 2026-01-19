<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocaleRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_label_renders_in_spanish_after_switch(): void
    {
        $user = User::factory()->create();

        // Switch locale to Spanish
        $this->get('/lang/es');

        $this->actingAs($user);

        // Visit users index page (auth protected) where sidebar is rendered
        $response = $this->get('/users');

        // Sidebar items
        $response->assertSee('Panel'); // Dashboard translated
        $response->assertSee('Usuarios'); // Users translated
    }
}
