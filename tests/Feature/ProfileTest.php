<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/dashboard/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put('/dashboard/profile', [
                'name' => 'Test User',
                'phone' => '081234567890',
                'email' => 'test@example.com',
            ]);

        $response->assertSessionHasNoErrors();
        
        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('081234567890', $user->phone);
        $this->assertSame('test@example.com', $user->email);
    }

    public function test_profile_password_can_be_updated(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this
            ->actingAs($user)
            ->put('/dashboard/profile/password', [
                'current_password' => 'old-password',
                'new_password' => 'new-password',
                'new_password_confirmation' => 'new-password',
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }
}
