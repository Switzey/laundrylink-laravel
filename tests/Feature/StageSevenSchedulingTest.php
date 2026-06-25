<?php

namespace Tests\Feature;

use App\Models\Cleaner;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderActivity;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StageSevenSchedulingTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_address(): void
    {
        $this->seed(DatabaseSeeder::class);

        $customer = $this->customer();

        $this->actingAs($customer)
            ->post(route('customer.addresses.store'), [
                'label' => 'Office',
                'address' => '22 Work Lane',
                'city' => 'Lagos',
                'phone' => '08055550000',
                'delivery_notes' => 'Ask for reception.',
                'is_default' => '1',
            ])
            ->assertRedirect(route('customer.addresses.index', absolute: false))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('addresses', [
            'user_id' => $customer->id,
            'label' => 'Office',
            'address' => '22 Work Lane',
            'city' => 'Lagos',
            'is_default' => true,
        ]);
    }

    public function test_customer_can_update_own_address(): void
    {
        $this->seed(DatabaseSeeder::class);

        $customer = $this->customer();
        $address = $customer->addresses()->firstOrFail();

        $this->actingAs($customer)
            ->patch(route('customer.addresses.update', $address), [
                'label' => 'Updated Home',
                'address' => '44 Updated Road',
                'city' => 'Lagos',
                'phone' => '08055550001',
                'delivery_notes' => 'Call from the gate.',
                'is_default' => '1',
            ])
            ->assertRedirect(route('customer.addresses.index', absolute: false))
            ->assertSessionHas('success');

        $address->refresh();

        $this->assertSame('Updated Home', $address->label);
        $this->assertSame('44 Updated Road', $address->address);
        $this->assertSame('Call from the gate.', $address->delivery_notes);
    }

    public function test_customer_cannot_update_another_customers_address(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = $this->customer();
        $other = User::query()
            ->where('role', 'customer')
            ->whereKeyNot($owner->id)
            ->firstOrFail();
        $address = $owner->addresses()->firstOrFail();

        $this->actingAs($other)
            ->patch(route('customer.addresses.update', $address), [
                'label' => 'Wrong edit',
                'address' => 'Wrong Road',
                'city' => 'Lagos',
            ])
            ->assertForbidden();

        $this->assertNotSame('Wrong edit', $address->fresh()->label);
    }

    public function test_customer_can_create_order_using_saved_address(): void
    {
        $this->seed(DatabaseSeeder::class);

        $customer = $this->customer();
        $address = $customer->addresses()->firstOrFail();
        $cleaner = $this->availableCleaner();
        $service = $cleaner->services()->where('is_active', true)->firstOrFail();

        $this->actingAs($customer)
            ->post(route('orders.store'), [
                'cleaner_id' => $cleaner->id,
                'services' => [
                    $service->id => 2,
                ],
                'pickup_address_id' => $address->id,
                'delivery_address_id' => $address->id,
                'pickup_date' => now()->addDay()->toDateString(),
                'pickup_time_window' => '8am - 10am',
                'delivery_date' => now()->addDays(3)->toDateString(),
                'delivery_time_window' => '2pm - 4pm',
            ])
            ->assertRedirect(route('customer.dashboard', absolute: false));

        $order = Order::query()->latest('id')->firstOrFail();

        $this->assertSame($address->address.', '.$address->city, $order->pickup_address);
        $this->assertSame($address->address.', '.$address->city, $order->delivery_address);
        $this->assertSame('8am - 10am', $order->pickup_time_window);
        $this->assertSame($address->delivery_notes, $order->pickup_notes);
        $this->assertDatabaseHas('order_activities', [
            'order_id' => $order->id,
            'action' => 'schedule_created',
        ]);
    }

    public function test_default_address_is_preselected_on_order_form(): void
    {
        $this->seed(DatabaseSeeder::class);

        $customer = $this->customer();
        $address = $customer->addresses()->where('is_default', true)->firstOrFail();

        $this->actingAs($customer)
            ->get(route('orders.create'))
            ->assertOk()
            ->assertSee('value="'.$address->id.'" selected', false)
            ->assertSee('default');
    }

    public function test_pickup_date_cannot_be_in_the_past(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$customer, $cleaner, $service] = $this->orderActors();

        $this->actingAs($customer)
            ->post(route('orders.store'), [
                'cleaner_id' => $cleaner->id,
                'services' => [$service->id => 1],
                'pickup_address' => '1 Past Street',
                'delivery_address' => '1 Past Street',
                'pickup_date' => now()->subDay()->toDateString(),
                'pickup_time_window' => '8am - 10am',
            ])
            ->assertSessionHasErrors('pickup_date');
    }

    public function test_delivery_date_cannot_be_before_pickup_date(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$customer, $cleaner, $service] = $this->orderActors();

        $this->actingAs($customer)
            ->post(route('orders.store'), [
                'cleaner_id' => $cleaner->id,
                'services' => [$service->id => 1],
                'pickup_address' => '1 Date Street',
                'delivery_address' => '1 Date Street',
                'pickup_date' => now()->addDays(3)->toDateString(),
                'pickup_time_window' => '8am - 10am',
                'delivery_date' => now()->addDay()->toDateString(),
            ])
            ->assertSessionHasErrors('delivery_date');
    }

    public function test_customer_can_reschedule_eligible_order(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$customer, $cleaner, $order] = $this->scheduledOrder();

        $this->actingAs($customer)
            ->patch(route('orders.reschedule.update', $order), [
                'pickup_date' => now()->addDays(2)->toDateString(),
                'pickup_time_window' => '10am - 12pm',
                'delivery_date' => now()->addDays(4)->toDateString(),
                'delivery_time_window' => '4pm - 6pm',
                'pickup_notes' => 'Use the side entrance.',
                'delivery_notes' => 'Drop at reception.',
            ])
            ->assertRedirect(route('orders.show', $order, absolute: false))
            ->assertSessionHas('success');

        $order->refresh();

        $this->assertSame('10am - 12pm', $order->pickup_time_window);
        $this->assertSame('Use the side entrance.', $order->pickup_notes);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $cleaner->user_id,
            'title' => 'Order schedule updated',
        ]);
        $this->assertDatabaseHas('order_activities', [
            'order_id' => $order->id,
            'user_id' => $customer->id,
            'action' => 'schedule_updated',
        ]);
    }

    public function test_customer_cannot_reschedule_another_customers_order(): void
    {
        $this->seed(DatabaseSeeder::class);

        [, , $order] = $this->scheduledOrder();
        $otherCustomer = User::query()
            ->where('role', 'customer')
            ->whereKeyNot($order->customer_id)
            ->firstOrFail();

        $this->actingAs($otherCustomer)
            ->patch(route('orders.reschedule.update', $order), [
                'pickup_date' => now()->addDays(2)->toDateString(),
                'pickup_time_window' => '10am - 12pm',
            ])
            ->assertForbidden();
    }

    public function test_customer_cannot_reschedule_completed_order(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$customer, , $order] = $this->scheduledOrder(['status' => 'completed']);

        $this->actingAs($customer)
            ->patch(route('orders.reschedule.update', $order), [
                'pickup_date' => now()->addDays(2)->toDateString(),
                'pickup_time_window' => '10am - 12pm',
            ])
            ->assertSessionHasErrors('schedule');
    }

    public function test_cleaner_can_view_own_schedule(): void
    {
        $this->seed(DatabaseSeeder::class);

        [, $cleaner, $order] = $this->scheduledOrder([
            'pickup_date' => today()->toDateString(),
            'pickup_time_window' => '8am - 10am',
        ]);

        $this->actingAs($cleaner->user)
            ->get(route('cleaner.schedule'))
            ->assertOk()
            ->assertSee('Pickup today')
            ->assertSee('Order #'.str_pad((string) $order->id, 4, '0', STR_PAD_LEFT));
    }

    public function test_cleaner_schedule_does_not_show_another_cleaners_orders(): void
    {
        $this->seed(DatabaseSeeder::class);

        [, $cleaner] = $this->scheduledOrder(['pickup_address' => 'Visible Schedule Address']);
        [, $otherCleaner, $otherOrder] = $this->scheduledOrder([
            'cleaner' => Cleaner::query()
                ->where('is_approved', true)
                ->where('is_available', true)
                ->whereKeyNot($cleaner->id)
                ->with('user')
                ->firstOrFail(),
            'pickup_address' => 'Hidden Schedule Address',
        ]);

        $this->actingAs($cleaner->user)
            ->get(route('cleaner.schedule'))
            ->assertOk()
            ->assertSee('Visible Schedule Address')
            ->assertDontSee('Hidden Schedule Address')
            ->assertDontSee('Order #'.str_pad((string) $otherOrder->id, 4, '0', STR_PAD_LEFT));

        $this->assertNotSame($cleaner->id, $otherCleaner->id);
    }

    public function test_rescheduling_creates_notification_and_order_activity(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$customer, $cleaner, $order] = $this->scheduledOrder();

        $this->actingAs($customer)
            ->patch(route('orders.reschedule.update', $order), [
                'pickup_date' => now()->addDays(5)->toDateString(),
                'pickup_time_window' => '12pm - 2pm',
                'delivery_date' => now()->addDays(6)->toDateString(),
                'delivery_time_window' => '2pm - 4pm',
            ])
            ->assertSessionHas('success');

        $this->assertSame(1, Notification::query()
            ->where('user_id', $cleaner->user_id)
            ->where('title', 'Order schedule updated')
            ->count());
        $this->assertSame(1, OrderActivity::query()
            ->where('order_id', $order->id)
            ->where('action', 'schedule_updated')
            ->count());
    }

    private function customer(): User
    {
        return User::query()->where('role', 'customer')->firstOrFail();
    }

    private function availableCleaner(): Cleaner
    {
        return Cleaner::query()
            ->where('is_approved', true)
            ->where('is_available', true)
            ->with('user')
            ->firstOrFail();
    }

    /**
     * @return array{0: User, 1: Cleaner, 2: \App\Models\Service}
     */
    private function orderActors(): array
    {
        $customer = $this->customer();
        $cleaner = $this->availableCleaner();
        $service = $cleaner->services()->where('is_active', true)->firstOrFail();

        return [$customer, $cleaner, $service];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array{0: User, 1: Cleaner, 2: Order}
     */
    private function scheduledOrder(array $overrides = []): array
    {
        $customer = $overrides['customer'] ?? $this->customer();
        $cleaner = $overrides['cleaner'] ?? $this->availableCleaner();

        $order = Order::query()->create([
            'customer_id' => $customer->id,
            'cleaner_id' => $cleaner->id,
            'pickup_address' => $overrides['pickup_address'] ?? '1 Schedule Street',
            'delivery_address' => $overrides['delivery_address'] ?? '2 Delivery Avenue',
            'pickup_date' => $overrides['pickup_date'] ?? now()->addDay()->toDateString(),
            'pickup_time_window' => $overrides['pickup_time_window'] ?? '8am - 10am',
            'delivery_date' => $overrides['delivery_date'] ?? now()->addDays(3)->toDateString(),
            'delivery_time_window' => $overrides['delivery_time_window'] ?? '2pm - 4pm',
            'status' => $overrides['status'] ?? 'pending',
            'subtotal' => 2000,
            'delivery_fee' => 1500,
            'platform_fee' => 500,
            'total' => 4000,
        ]);

        return [$customer, $cleaner, $order];
    }
}
