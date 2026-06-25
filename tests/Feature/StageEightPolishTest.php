<?php

namespace Tests\Feature;

use App\Models\Cleaner;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StageEightPolishTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_reports(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.reports'))
            ->assertOk()
            ->assertSee('Platform reporting')
            ->assertSee('Orders by status')
            ->assertSee('Top cleaners by revenue');
    }

    public function test_cleaner_can_view_own_reports(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cleaner = Cleaner::query()->where('user_id', User::query()->where('email', 'cleaner@example.com')->value('id'))->firstOrFail();

        $this->actingAs($cleaner->user)
            ->get(route('cleaner.reports'))
            ->assertOk()
            ->assertSee($cleaner->business_name)
            ->assertSee('Most ordered services')
            ->assertSee('Estimated earnings');
    }

    public function test_cleaner_cannot_view_another_cleaners_report_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cleaner = Cleaner::query()->where('user_id', User::query()->where('email', 'cleaner@example.com')->value('id'))->firstOrFail();
        $otherCleaner = Cleaner::query()
            ->where('is_approved', true)
            ->whereKeyNot($cleaner->id)
            ->firstOrFail();
        $otherService = Service::query()->create([
            'cleaner_id' => $otherCleaner->id,
            'name' => 'Private Velvet Service',
            'description' => 'Other cleaner report data.',
            'price' => 9999,
            'unit' => 'flat',
            'is_active' => true,
        ]);
        $otherOrder = Order::query()->create([
            'customer_id' => User::query()->where('role', 'customer')->firstOrFail()->id,
            'cleaner_id' => $otherCleaner->id,
            'pickup_address' => 'Hidden pickup',
            'delivery_address' => 'Hidden delivery',
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time_window' => '8am - 10am',
            'status' => 'pending',
            'total' => 9999,
        ]);
        OrderItem::query()->create([
            'order_id' => $otherOrder->id,
            'service_id' => $otherService->id,
            'quantity' => 1,
            'price' => $otherService->price,
        ]);

        $this->actingAs($cleaner->user)
            ->get(route('cleaner.reports'))
            ->assertOk()
            ->assertDontSee('Private Velvet Service')
            ->assertDontSee($otherCleaner->business_name);
    }

    public function test_customer_order_filters_work(): void
    {
        $this->seed(DatabaseSeeder::class);

        $customer = User::query()->where('email', 'customer@example.com')->firstOrFail();
        $cleaner = Cleaner::query()->where('is_approved', true)->where('is_available', true)->firstOrFail();
        $pendingOrder = Order::query()->create([
            'customer_id' => $customer->id,
            'cleaner_id' => $cleaner->id,
            'pickup_address' => 'Filter pending pickup',
            'delivery_address' => 'Filter pending delivery',
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time_window' => '8am - 10am',
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'total' => 3000,
        ]);
        $completedOrder = Order::query()->create([
            'customer_id' => $customer->id,
            'cleaner_id' => $cleaner->id,
            'pickup_address' => 'Filter completed pickup',
            'delivery_address' => 'Filter completed delivery',
            'pickup_date' => now()->addDays(2)->toDateString(),
            'pickup_time_window' => '10am - 12pm',
            'status' => 'completed',
            'payment_status' => 'unpaid',
            'total' => 4000,
        ]);

        $this->actingAs($customer)
            ->get(route('customer.dashboard', ['filter' => 'pending']))
            ->assertOk()
            ->assertSee('#'.str_pad((string) $pendingOrder->id, 4, '0', STR_PAD_LEFT))
            ->assertDontSee('#'.str_pad((string) $completedOrder->id, 4, '0', STR_PAD_LEFT));
    }

    public function test_cleaner_search_by_city_works(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('cleaners.index', ['city' => 'Lagos']))
            ->assertOk()
            ->assertSee('FreshFold Laundry')
            ->assertDontSee('QuickPress Cleaners');
    }

    public function test_cleaner_search_by_service_works(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('cleaners.index', ['search' => 'Agbada']))
            ->assertOk()
            ->assertSee('QuickPress Cleaners')
            ->assertDontSee('FreshFold Laundry');
    }

    public function test_public_listing_hides_unapproved_cleaners(): void
    {
        $this->seed(DatabaseSeeder::class);

        $unapproved = Cleaner::query()->where('is_approved', false)->firstOrFail();

        $this->get(route('cleaners.index'))
            ->assertOk()
            ->assertDontSee($unapproved->business_name);
    }

    public function test_public_listing_hides_unavailable_cleaners(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cleaner = Cleaner::query()->where('is_approved', true)->where('is_available', true)->firstOrFail();
        $cleaner->update(['is_available' => false]);

        $this->get(route('cleaners.index'))
            ->assertOk()
            ->assertDontSee($cleaner->business_name);
    }

    public function test_order_detail_is_protected_by_ownership(): void
    {
        $this->seed(DatabaseSeeder::class);

        $order = Order::query()->whereNotNull('customer_id')->firstOrFail();
        $otherCustomer = User::query()
            ->where('role', 'customer')
            ->whereKeyNot($order->customer_id)
            ->firstOrFail();

        $this->actingAs($otherCustomer)
            ->get(route('orders.show', $order))
            ->assertForbidden();
    }

    public function test_readme_installation_commands_are_documented(): void
    {
        $readme = file_get_contents(base_path('README.md'));

        $this->assertStringContainsString('composer install', $readme);
        $this->assertStringContainsString('php artisan migrate --seed', $readme);
        $this->assertStringContainsString('npm run build', $readme);
        $this->assertStringContainsString('admin@example.com', $readme);
        $this->assertStringContainsString('customer@example.com', $readme);
        $this->assertStringContainsString('cleaner@example.com', $readme);
    }
}
