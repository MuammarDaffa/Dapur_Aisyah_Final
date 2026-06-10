<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\CateringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_services_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/services');

        $response->assertOk();
    }
}
