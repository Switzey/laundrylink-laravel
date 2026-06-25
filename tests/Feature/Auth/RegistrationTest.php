<?php

namespace Tests\Feature\Auth;

use App\Models\Cleaner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'customer',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('client.dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'customer',
        ]);
    }

    public function test_new_cleaners_can_register_with_pending_profile(): void
    {
        $response = $this->post('/register', [
            'name' => 'Cleaner User',
            'email' => 'cleaner@example.com',
            'role' => 'cleaner',
            'phone' => '08090000000',
            'address' => '10 Test Street',
            'city' => 'Lagos',
            'business_name' => 'Test Cleaners',
            'description' => 'Careful laundry service.',
            'turnaround_time' => '48 hours',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('vendor.dashboard', absolute: false));

        $user = User::query()->where('email', 'cleaner@example.com')->firstOrFail();

        $this->assertSame('cleaner', $user->role);
        $this->assertTrue(Cleaner::query()
            ->where('user_id', $user->id)
            ->where('business_name', 'Test Cleaners')
            ->where('is_approved', false)
            ->exists());
    }
}
