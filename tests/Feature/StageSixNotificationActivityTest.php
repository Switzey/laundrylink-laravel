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

class StageSixNotificationActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_is_created_when_order_is_created(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$customer, $cleaner, $order] = $this->createCustomerOrder();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $cleaner->user_id,
            'type' => 'order_created',
        ]);

        $this->assertDatabaseHas('order_activities', [
            'order_id' => $order->id,
            'user_id' => $customer->id,
            'action' => 'order_created',
        ]);
    }

    public function test_cleaner_receives_notification_when_order_is_created(): void
    {
        $this->seed(DatabaseSeeder::class);

        [, $cleaner] = $this->createCustomerOrder();

        $this->actingAs($cleaner->user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('New laundry order');
    }

    public function test_customer_receives_notification_when_cleaner_updates_order_status(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$customer, $cleaner, $order] = $this->createCustomerOrder();

        $this->actingAs($cleaner->user)
            ->patch(route('cleaner.orders.status', $order), ['status' => 'accepted']);

        $this->actingAs($cleaner->user)
            ->patch(route('cleaner.orders.status', $order->fresh()), ['status' => 'picked_up']);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'type' => 'order_status_updated',
        ]);
    }

    public function test_user_cannot_view_or_update_another_users_notifications(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('role', 'customer')->firstOrFail();
        $other = User::query()
            ->where('role', 'customer')
            ->whereKeyNot($owner->id)
            ->firstOrFail();

        $notification = Notification::query()->create([
            'user_id' => $owner->id,
            'title' => 'Private notice for owner',
            'message' => 'Only the owner should see this.',
            'type' => 'general',
        ]);

        $this->actingAs($other)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertDontSee('Private notice for owner');

        $this->actingAs($other)
            ->patch(route('notifications.read', $notification))
            ->assertForbidden();
    }

    public function test_user_can_mark_own_notification_as_read(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('role', 'customer')->firstOrFail();
        $notification = Notification::query()->create([
            'user_id' => $user->id,
            'title' => 'Readable notice',
            'message' => 'Mark this notification as read.',
            'type' => 'general',
        ]);

        $this->actingAs($user)
            ->patch(route('notifications.read', $notification))
            ->assertSessionHas('success');

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_own_notifications_as_read(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('role', 'customer')->firstOrFail();
        $other = User::query()
            ->where('role', 'customer')
            ->whereKeyNot($user->id)
            ->firstOrFail();

        Notification::query()->create([
            'user_id' => $user->id,
            'title' => 'First unread',
            'message' => 'One',
            'type' => 'general',
        ]);
        Notification::query()->create([
            'user_id' => $user->id,
            'title' => 'Second unread',
            'message' => 'Two',
            'type' => 'general',
        ]);
        $otherNotification = Notification::query()->create([
            'user_id' => $other->id,
            'title' => 'Other unread',
            'message' => 'Three',
            'type' => 'general',
        ]);

        $this->actingAs($user)
            ->patch(route('notifications.read-all'))
            ->assertSessionHas('success');

        $this->assertSame(0, Notification::query()->where('user_id', $user->id)->whereNull('read_at')->count());
        $this->assertNull($otherNotification->fresh()->read_at);
    }

    public function test_order_activity_is_logged_when_order_status_changes(): void
    {
        $this->seed(DatabaseSeeder::class);

        [, $cleaner, $order] = $this->createCustomerOrder();

        $this->actingAs($cleaner->user)
            ->patch(route('cleaner.orders.status', $order), ['status' => 'accepted']);

        $this->assertDatabaseHas('order_activities', [
            'order_id' => $order->id,
            'user_id' => $cleaner->user_id,
            'action' => 'order_accepted',
        ]);
    }

    public function test_order_detail_shows_activity_timeline_to_authorized_customer(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$customer, , $order] = $this->createCustomerOrder();

        $this->actingAs($customer)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('Order activity')
            ->assertSee('Order was created');
    }

    public function test_unauthorized_user_cannot_view_another_order_activity_timeline(): void
    {
        $this->seed(DatabaseSeeder::class);

        [, , $order] = $this->createCustomerOrder();
        $otherCustomer = User::query()
            ->where('role', 'customer')
            ->whereKeyNot($order->customer_id)
            ->firstOrFail();

        OrderActivity::query()->create([
            'order_id' => $order->id,
            'user_id' => $order->customer_id,
            'action' => 'private_activity',
            'description' => 'Private timeline entry.',
        ]);

        $this->actingAs($otherCustomer)
            ->get(route('orders.show', $order))
            ->assertForbidden();
    }

    /**
     * @return array{0: User, 1: Cleaner, 2: Order}
     */
    private function createCustomerOrder(): array
    {
        $customer = User::query()->where('role', 'customer')->firstOrFail();
        $cleaner = Cleaner::query()
            ->where('is_approved', true)
            ->where('is_available', true)
            ->with(['user', 'services' => fn ($query) => $query->where('is_active', true)])
            ->firstOrFail();
        $service = $cleaner->services->firstOrFail();

        $this->actingAs($customer)
            ->post(route('orders.store'), [
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
            ])
            ->assertRedirect(route('customer.dashboard', absolute: false));

        $order = Order::query()->latest('id')->firstOrFail();

        return [$customer, $cleaner, $order];
    }
}
