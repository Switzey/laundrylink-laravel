<?php

namespace Tests\Feature;

use App\Models\Cleaner;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StageTwoFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_protected_dashboards_reject_wrong_roles(): void
    {
        $this->seed(DatabaseSeeder::class);

        $customer = User::query()->where('role', 'customer')->firstOrFail();
        $cleaner = User::query()->where('role', 'cleaner')->firstOrFail();
        $admin = User::query()->where('role', 'admin')->firstOrFail();

        $this->actingAs($customer)->get('/cleaner/dashboard')->assertForbidden();
        $this->actingAs($customer)->get('/admin/dashboard')->assertForbidden();

        $this->actingAs($cleaner)->get('/customer/dashboard')->assertForbidden();
        $this->actingAs($cleaner)->get('/admin/dashboard')->assertForbidden();

        $this->actingAs($admin)->get('/customer/dashboard')->assertForbidden();
        $this->actingAs($admin)->get('/cleaner/dashboard')->assertForbidden();
    }

    public function test_customer_can_create_order_without_submitting_prices(): void
    {
        $this->seed(DatabaseSeeder::class);

        $customer = User::query()->where('role', 'customer')->firstOrFail();
        $cleaner = Cleaner::query()->where('is_approved', true)->with('services')->firstOrFail();
        $service = $cleaner->services->first();

        $response = $this->actingAs($customer)->post('/orders', [
            'cleaner_id' => $cleaner->id,
            'services' => [
                $service->id => 2,
            ],
            'pickup_address' => '1 Customer Street',
            'delivery_address' => '1 Customer Street',
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time_window' => '10am - 12pm',
            'delivery_date' => now()->addDays(3)->toDateString(),
            'notes' => 'Please call before pickup.',
        ]);

        $response->assertRedirect(route('customer.dashboard', absolute: false));

        $order = Order::query()->latest('id')->firstOrFail();

        $this->assertSame($customer->id, $order->customer_id);
        $this->assertSame($cleaner->id, $order->cleaner_id);
        $this->assertSame('pending', $order->status);
        $this->assertSame('1500.00', $order->delivery_fee);
        $this->assertSame('500.00', $order->platform_fee);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'service_id' => $service->id,
            'quantity' => 2,
            'price' => $service->price,
        ]);
    }

    public function test_cleaner_can_advance_assigned_order_status(): void
    {
        $this->seed(DatabaseSeeder::class);

        $order = Order::query()->where('status', 'pending')->with('cleaner.user')->firstOrFail();
        $cleanerUser = $order->cleaner->user;

        $this->actingAs($cleanerUser)
            ->patch(route('cleaner.orders.status', $order), ['status' => 'accepted'])
            ->assertSessionHas('success');

        $this->assertSame('accepted', $order->fresh()->status);

        $this->actingAs($cleanerUser)
            ->patch(route('cleaner.orders.status', $order), ['status' => 'completed'])
            ->assertSessionHas('error');

        $this->assertSame('accepted', $order->fresh()->status);
    }

    public function test_admin_can_approve_and_unapprove_cleaner(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('role', 'admin')->firstOrFail();
        $cleaner = Cleaner::query()->where('is_approved', false)->firstOrFail();

        $this->actingAs($admin)
            ->patch(route('admin.cleaners.approve', $cleaner))
            ->assertSessionHas('success');

        $this->assertTrue($cleaner->fresh()->is_approved);

        $this->actingAs($admin)
            ->patch(route('admin.cleaners.unapprove', $cleaner))
            ->assertSessionHas('success');

        $this->assertFalse($cleaner->fresh()->is_approved);
    }
}
