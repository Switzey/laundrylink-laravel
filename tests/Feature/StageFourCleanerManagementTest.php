<?php

namespace Tests\Feature;

use App\Models\Cleaner;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StageFourCleanerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_cleaner_can_view_own_profile_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cleaner = Cleaner::query()->where('is_approved', true)->with('user')->firstOrFail();

        $this->actingAs($cleaner->user)
            ->get(route('cleaner.profile.edit'))
            ->assertOk()
            ->assertSee($cleaner->business_name);
    }

    public function test_cleaner_can_update_own_profile(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cleaner = Cleaner::query()->where('is_approved', true)->with('user')->firstOrFail();

        $this->actingAs($cleaner->user)
            ->patch(route('cleaner.profile.update'), [
                'business_name' => 'Updated FreshFold Studio',
                'description' => 'Updated profile copy.',
                'address' => '15 Updated Street',
                'city' => 'Lagos',
                'phone' => '08090000001',
                'turnaround_time' => '24 hours',
                'opening_hours' => 'Mon-Fri, 8am-5pm',
                'is_available' => '0',
            ])
            ->assertRedirect(route('cleaner.profile.edit', absolute: false))
            ->assertSessionHas('success');

        $cleaner->refresh();

        $this->assertSame('Updated FreshFold Studio', $cleaner->business_name);
        $this->assertSame('Mon-Fri, 8am-5pm', $cleaner->opening_hours);
        $this->assertFalse($cleaner->is_available);
    }

    public function test_cleaner_can_create_service(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cleaner = Cleaner::query()->where('is_approved', true)->with('user')->firstOrFail();

        $this->actingAs($cleaner->user)
            ->post(route('cleaner.services.store'), [
                'name' => 'Premium Press',
                'description' => 'Careful pressing for premium garments.',
                'price' => '2500',
                'unit' => 'per_item',
                'is_active' => '1',
            ])
            ->assertRedirect(route('cleaner.services.index', absolute: false))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('services', [
            'cleaner_id' => $cleaner->id,
            'name' => 'Premium Press',
            'price' => '2500',
            'unit' => 'per_item',
            'is_active' => true,
        ]);
    }

    public function test_cleaner_can_view_service_management_pages(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cleaner = Cleaner::query()->where('is_approved', true)->with(['user', 'services'])->firstOrFail();
        $service = $cleaner->services->firstOrFail();

        $this->actingAs($cleaner->user)
            ->get(route('cleaner.services.index'))
            ->assertOk()
            ->assertSee($service->name);

        $this->actingAs($cleaner->user)
            ->get(route('cleaner.services.create'))
            ->assertOk()
            ->assertSee('Add a service');

        $this->actingAs($cleaner->user)
            ->get(route('cleaner.services.edit', $service))
            ->assertOk()
            ->assertSee($service->name);
    }

    public function test_cleaner_can_update_own_service(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cleaner = Cleaner::query()->where('is_approved', true)->with(['user', 'services'])->firstOrFail();
        $service = $cleaner->services->firstOrFail();

        $this->actingAs($cleaner->user)
            ->patch(route('cleaner.services.update', $service), [
                'name' => 'Updated Shirt Care',
                'description' => 'Updated description.',
                'price' => '1500',
                'unit' => 'per_item',
                'is_active' => '0',
            ])
            ->assertRedirect(route('cleaner.services.index', absolute: false))
            ->assertSessionHas('success');

        $service->refresh();

        $this->assertSame('Updated Shirt Care', $service->name);
        $this->assertSame('1500.00', $service->price);
        $this->assertFalse($service->is_active);
    }

    public function test_cleaner_cannot_update_another_cleaners_service(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cleaner = Cleaner::query()->where('is_approved', true)->with('user')->firstOrFail();
        $otherService = Service::query()
            ->where('cleaner_id', '!=', $cleaner->id)
            ->firstOrFail();

        $this->actingAs($cleaner->user)
            ->patch(route('cleaner.services.update', $otherService), [
                'name' => 'Cross Edit Attempt',
                'description' => 'Should not work.',
                'price' => '2000',
                'unit' => 'flat',
                'is_active' => '1',
            ])
            ->assertForbidden();

        $this->assertNotSame('Cross Edit Attempt', $otherService->fresh()->name);
    }

    public function test_inactive_services_do_not_show_publicly(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cleaner = Cleaner::query()->where('is_approved', true)->where('is_available', true)->firstOrFail();
        $service = $cleaner->services()->firstOrFail();

        $service->update(['is_active' => false]);

        $this->get(route('cleaners.show', $cleaner))
            ->assertOk()
            ->assertDontSee($service->name);
    }

    public function test_unapproved_cleaners_do_not_show_publicly(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cleaner = Cleaner::query()->where('is_approved', false)->firstOrFail();

        $this->get(route('cleaners.index'))
            ->assertOk()
            ->assertDontSee($cleaner->business_name);

        $this->get(route('cleaners.show', $cleaner))
            ->assertNotFound();
    }

    public function test_customer_cannot_order_from_unapproved_cleaner(): void
    {
        $this->seed(DatabaseSeeder::class);

        $customer = User::query()->where('role', 'customer')->firstOrFail();
        $cleaner = Cleaner::query()->where('is_approved', false)->with('services')->firstOrFail();
        $service = $cleaner->services->firstOrFail();

        $this->actingAs($customer)
            ->post(route('orders.store'), [
                'cleaner_id' => $cleaner->id,
                'services' => [
                    $service->id => 1,
                ],
                'pickup_address' => '1 Customer Street',
                'delivery_address' => '1 Customer Street',
                'pickup_date' => now()->addDay()->toDateString(),
                'pickup_time_window' => '10am - 12pm',
                'delivery_date' => now()->addDays(3)->toDateString(),
            ])
            ->assertSessionHasErrors('cleaner_id');

        $this->assertDatabaseMissing('orders', [
            'customer_id' => $customer->id,
            'cleaner_id' => $cleaner->id,
        ]);
    }
}
