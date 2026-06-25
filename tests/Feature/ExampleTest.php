<?php

namespace Tests\Feature;

use App\Models\Cleaner;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_stage_two_pages_render_successfully_for_matching_roles(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cleaner = Cleaner::query()->where('is_approved', true)->firstOrFail();
        $customerUser = User::query()->where('role', 'customer')->firstOrFail();
        $cleanerUser = $cleaner->user;
        $adminUser = User::query()->where('role', 'admin')->firstOrFail();

        $this->assertSame(200, $this->get('/')->getStatusCode(), 'Landing page failed.');
        $this->assertSame(200, $this->get('/cleaners')->getStatusCode(), 'Cleaner listing failed.');
        $this->assertSame(200, $this->get("/cleaners/{$cleaner->id}")->getStatusCode(), 'Cleaner detail failed.');

        $this->assertSame(200, $this->actingAs($customerUser)->get('/customer/dashboard')->getStatusCode(), 'Customer dashboard failed.');
        $this->assertSame(200, $this->actingAs($customerUser)->get("/orders/create?cleaner={$cleaner->id}")->getStatusCode(), 'Order create failed.');
        $this->assertSame(200, $this->actingAs($cleanerUser)->get('/cleaner/dashboard')->getStatusCode(), 'Cleaner dashboard failed.');
        $this->assertSame(200, $this->actingAs($adminUser)->get('/admin/dashboard')->getStatusCode(), 'Admin dashboard failed.');
    }
}
