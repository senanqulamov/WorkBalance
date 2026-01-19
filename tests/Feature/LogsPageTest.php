<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_logs_page_can_be_accessed_by_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/logs');

        $response->assertStatus(200);
        $response->assertSeeLivewire('logs.index');
    }

    public function test_logs_page_requires_authentication(): void
    {
        $response = $this->get('/logs');

        $response->assertRedirect('/login');
    }
}
