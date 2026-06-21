<?php

namespace Tests\Feature;

use App\Models\Cleaner;
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

    public function test_stage_one_pages_render_successfully(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cleaner = Cleaner::query()->where('is_approved', true)->firstOrFail();

        $routes = [
            '/',
            '/customer/dashboard',
            '/cleaners',
            "/cleaners/{$cleaner->id}",
            "/orders/create?cleaner={$cleaner->id}",
            '/cleaner/dashboard',
            '/admin/dashboard',
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertOk();
        }
    }
}
