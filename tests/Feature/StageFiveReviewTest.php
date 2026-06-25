<?php

namespace Tests\Feature;

use App\Models\Cleaner;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StageFiveReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_review_own_completed_paid_order(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$customer, $cleaner, $order] = $this->makeReviewableOrder();

        $this->actingAs($customer)
            ->get(route('orders.review.create', $order))
            ->assertOk()
            ->assertSee($cleaner->business_name);

        $this->actingAs($customer)
            ->post(route('orders.review.store', $order), [
                'rating' => 4,
                'comment' => 'Careful packaging and quick delivery.',
            ])
            ->assertRedirect(route('orders.show', $order, absolute: false))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'cleaner_id' => $cleaner->id,
            'rating' => 4,
            'comment' => 'Careful packaging and quick delivery.',
        ]);

        $this->assertSame('4.0', $cleaner->fresh()->rating);
    }

    public function test_customer_cannot_review_unpaid_order(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$customer, , $order] = $this->makeReviewableOrder([
            'payment_status' => 'unpaid',
            'paid_at' => null,
        ]);

        $this->actingAs($customer)
            ->post(route('orders.review.store', $order), [
                'rating' => 5,
                'comment' => 'Should fail.',
            ])
            ->assertSessionHasErrors('review');

        $this->assertDatabaseMissing('reviews', [
            'order_id' => $order->id,
        ]);
    }

    public function test_customer_cannot_review_incomplete_order(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$customer, , $order] = $this->makeReviewableOrder([
            'status' => 'accepted',
        ]);

        $this->actingAs($customer)
            ->post(route('orders.review.store', $order), [
                'rating' => 5,
                'comment' => 'Should fail.',
            ])
            ->assertSessionHasErrors('review');

        $this->assertDatabaseMissing('reviews', [
            'order_id' => $order->id,
        ]);
    }

    public function test_customer_cannot_review_another_customers_order(): void
    {
        $this->seed(DatabaseSeeder::class);

        [, , $order] = $this->makeReviewableOrder();
        $otherCustomer = User::query()
            ->where('role', 'customer')
            ->whereKeyNot($order->customer_id)
            ->firstOrFail();

        $this->actingAs($otherCustomer)
            ->post(route('orders.review.store', $order), [
                'rating' => 5,
                'comment' => 'Should fail.',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('reviews', [
            'order_id' => $order->id,
        ]);
    }

    public function test_customer_cannot_review_same_order_twice(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$customer, $cleaner, $order] = $this->makeReviewableOrder();

        Review::query()->create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'cleaner_id' => $cleaner->id,
            'rating' => 5,
            'comment' => 'Already reviewed.',
        ]);

        Review::refreshCleanerRating($cleaner);

        $this->actingAs($customer)
            ->post(route('orders.review.store', $order), [
                'rating' => 3,
                'comment' => 'Second attempt.',
            ])
            ->assertSessionHasErrors('review');

        $this->assertSame(1, Review::query()->where('order_id', $order->id)->count());
    }

    public function test_cleaner_rating_updates_after_review(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$firstCustomer, $cleaner, $firstOrder] = $this->makeReviewableOrder();
        $secondCustomer = User::query()
            ->where('role', 'customer')
            ->whereKeyNot($firstCustomer->id)
            ->firstOrFail();
        $secondOrder = $this->makeCompletedPaidOrder($secondCustomer, $cleaner);

        $this->actingAs($firstCustomer)->post(route('orders.review.store', $firstOrder), [
            'rating' => 5,
            'comment' => 'Excellent.',
        ]);

        $this->actingAs($secondCustomer)->post(route('orders.review.store', $secondOrder), [
            'rating' => 3,
            'comment' => 'Solid.',
        ]);

        $this->assertSame('4.0', $cleaner->fresh()->rating);
    }

    public function test_cleaner_can_see_reviews(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$customer, $cleaner, $order] = $this->makeReviewableOrder();

        Review::query()->create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'cleaner_id' => $cleaner->id,
            'rating' => 5,
            'comment' => 'Wonderful service.',
        ]);

        Review::refreshCleanerRating($cleaner);

        $this->actingAs($cleaner->user)
            ->get(route('cleaner.dashboard'))
            ->assertOk()
            ->assertSee('Customer reviews')
            ->assertSee('Wonderful service.');
    }

    public function test_admin_can_delete_review(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$customer, $cleaner, $order] = $this->makeReviewableOrder();
        $review = Review::query()->create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'cleaner_id' => $cleaner->id,
            'rating' => 2,
            'comment' => 'Inappropriate content.',
        ]);

        Review::refreshCleanerRating($cleaner);

        $admin = User::query()->where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.reviews.index'))
            ->assertOk()
            ->assertSee('Inappropriate content.');

        $this->actingAs($admin)
            ->delete(route('admin.reviews.destroy', $review))
            ->assertRedirect(route('admin.reviews.index', absolute: false))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }

    public function test_deleting_review_recalculates_cleaner_rating(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$firstCustomer, $cleaner, $firstOrder] = $this->makeReviewableOrder();
        $secondCustomer = User::query()
            ->where('role', 'customer')
            ->whereKeyNot($firstCustomer->id)
            ->firstOrFail();
        $secondOrder = $this->makeCompletedPaidOrder($secondCustomer, $cleaner);

        $firstReview = Review::query()->create([
            'order_id' => $firstOrder->id,
            'customer_id' => $firstCustomer->id,
            'cleaner_id' => $cleaner->id,
            'rating' => 5,
            'comment' => 'Excellent.',
        ]);

        Review::query()->create([
            'order_id' => $secondOrder->id,
            'customer_id' => $secondCustomer->id,
            'cleaner_id' => $cleaner->id,
            'rating' => 3,
            'comment' => 'Solid.',
        ]);

        Review::refreshCleanerRating($cleaner);

        $this->assertSame('4.0', $cleaner->fresh()->rating);

        $admin = User::query()->where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('admin.reviews.destroy', $firstReview));

        $this->assertSame('3.0', $cleaner->fresh()->rating);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array{0: User, 1: Cleaner, 2: Order}
     */
    private function makeReviewableOrder(array $overrides = []): array
    {
        $customer = User::query()->where('role', 'customer')->firstOrFail();
        $cleaner = Cleaner::query()
            ->where('is_approved', true)
            ->where('is_available', true)
            ->whereDoesntHave('reviews')
            ->with('user')
            ->firstOrFail();

        return [
            $customer,
            $cleaner,
            $this->makeCompletedPaidOrder($customer, $cleaner, $overrides),
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeCompletedPaidOrder(User $customer, Cleaner $cleaner, array $overrides = []): Order
    {
        return Order::query()->create($overrides + [
            'customer_id' => $customer->id,
            'cleaner_id' => $cleaner->id,
            'pickup_address' => '1 Review Street',
            'delivery_address' => '1 Review Street',
            'pickup_date' => now()->subDays(5)->toDateString(),
            'delivery_date' => now()->subDays(2)->toDateString(),
            'status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => 5000,
            'delivery_fee' => 1500,
            'platform_fee' => 500,
            'total' => 7000,
            'paid_at' => now()->subDay(),
        ]);
    }
}
