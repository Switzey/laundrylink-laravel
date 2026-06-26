<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Cleaner;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (User::query()->where('email', 'admin@example.com')->exists()) {
            return;
        }

        DB::beginTransaction();

        try {
        User::query()->create([
            'name' => 'Ada Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '08010000001',
            'address' => '12 Marina Road, Lagos',
        ]);

        $customers = collect([
            [
                'name' => 'Tola Martins',
                'email' => 'customer@example.com',
                'phone' => '08020000001',
                'address' => '14 Admiralty Way, Lekki',
                'city' => 'Lagos',
            ],
            [
                'name' => 'Kemi Johnson',
                'email' => 'kemi@example.com',
                'phone' => '08020000002',
                'address' => '22 Aminu Kano Crescent, Wuse',
                'city' => 'Abuja',
            ],
        ])->map(fn (array $customer) => User::query()->create([
            'name' => $customer['name'],
            'email' => $customer['email'],
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => $customer['phone'],
            'address' => $customer['address'],
        ]));

        $customers->each(function (User $customer, int $index): void {
            Address::query()->create([
                'user_id' => $customer->id,
                'label' => $index === 0 ? 'Home' : 'Apartment',
                'address' => $customer->address,
                'city' => $index === 0 ? 'Lagos' : 'Abuja',
                'phone' => $customer->phone,
                'is_default' => true,
                'delivery_notes' => $index === 0 ? 'Call at the estate gate before arrival.' : 'Use the main entrance reception.',
            ]);
        });

        $cleanerProfiles = [
            [
                'user' => ['name' => 'Bisi Fresh', 'email' => 'cleaner@example.com', 'phone' => '08030000001'],
                'business_name' => 'FreshFold Laundry',
                'description' => 'Neighborhood wash, fold, and dry-cleaning with careful packaging for busy homes.',
                'address' => '8 Admiralty Road',
                'city' => 'Lagos',
                'phone' => '08030000001',
                'rating' => 0,
                'turnaround_time' => '24-48 hours',
                'opening_hours' => 'Mon-Sat, 8am-6pm',
                'is_available' => true,
                'is_approved' => true,
                'services' => [
                    ['name' => 'Shirt Laundry', 'description' => 'Washed, pressed, and folded shirts.', 'price' => 1200, 'unit' => 'per_item', 'is_active' => true],
                    ['name' => 'Suit Dry Cleaning', 'description' => 'Two-piece suit care with finishing.', 'price' => 6500, 'unit' => 'per_item', 'is_active' => true],
                    ['name' => 'Wash & Fold', 'description' => 'Everyday clothing by weight.', 'price' => 1800, 'unit' => 'per_kg', 'is_active' => true],
                    ['name' => 'Bedding Set', 'description' => 'Sheets, pillowcases, and duvet cover.', 'price' => 5000, 'unit' => 'flat', 'is_active' => true],
                ],
            ],
            [
                'user' => ['name' => 'Ife Clean', 'email' => 'ife@quickpress.test', 'phone' => '08030000002'],
                'business_name' => 'QuickPress Cleaners',
                'description' => 'Fast pressing, stain treatment, and delivery slots for office wear.',
                'address' => '31 Allen Avenue',
                'city' => 'Ikeja',
                'phone' => '08030000002',
                'rating' => 0,
                'turnaround_time' => 'Same day',
                'opening_hours' => 'Mon-Fri, 7am-7pm',
                'is_available' => true,
                'is_approved' => true,
                'services' => [
                    ['name' => 'Office Shirt', 'description' => 'Crisp shirt laundry and press.', 'price' => 1000, 'unit' => 'per_item', 'is_active' => true],
                    ['name' => 'Trouser Press', 'description' => 'Steam press and fold.', 'price' => 900, 'unit' => 'per_item', 'is_active' => true],
                    ['name' => 'Agbada Care', 'description' => 'Special care for traditional outfits.', 'price' => 7500, 'unit' => 'per_item', 'is_active' => true],
                    ['name' => 'Express Wash', 'description' => 'Priority wash bundle.', 'price' => 4000, 'unit' => 'flat', 'is_active' => true],
                ],
            ],
            [
                'user' => ['name' => 'Musa Sparkle', 'email' => 'musa@sparkle.test', 'phone' => '08030000003'],
                'business_name' => 'SparkleCare Drycleaners',
                'description' => 'Premium fabric care for delicate garments, gowns, curtains, and linens.',
                'address' => '16 Gana Street',
                'city' => 'Abuja',
                'phone' => '08030000003',
                'rating' => 0,
                'turnaround_time' => '48 hours',
                'opening_hours' => 'Mon-Sat, 9am-5pm',
                'is_available' => true,
                'is_approved' => true,
                'services' => [
                    ['name' => 'Dress Dry Cleaning', 'description' => 'Gentle dry cleaning for dresses.', 'price' => 4500, 'unit' => 'per_item', 'is_active' => true],
                    ['name' => 'Curtain Cleaning', 'description' => 'Deep clean for curtains by weight.', 'price' => 2500, 'unit' => 'per_kg', 'is_active' => true],
                    ['name' => 'Delicate Fabric Care', 'description' => 'Silk, lace, and special garments.', 'price' => 6000, 'unit' => 'per_item', 'is_active' => true],
                    ['name' => 'Duvet Cleaning', 'description' => 'Large duvet wash and dry.', 'price' => 7000, 'unit' => 'flat', 'is_active' => true],
                ],
            ],
            [
                'user' => ['name' => 'Nora Bright', 'email' => 'nora@brightwash.test', 'phone' => '08030000004'],
                'business_name' => 'BrightWash Hub',
                'description' => 'Upcoming cleaner awaiting platform approval with family laundry packages.',
                'address' => '4 Stadium Road',
                'city' => 'Port Harcourt',
                'phone' => '08030000004',
                'rating' => 0,
                'turnaround_time' => '72 hours',
                'opening_hours' => 'Mon-Sat, 8am-4pm',
                'is_available' => true,
                'is_approved' => false,
                'services' => [
                    ['name' => 'Family Wash Bag', 'description' => 'Mixed family laundry package.', 'price' => 9500, 'unit' => 'flat', 'is_active' => true],
                    ['name' => 'Children Wear', 'description' => 'Gentle wash for kids clothing.', 'price' => 800, 'unit' => 'per_item', 'is_active' => true],
                    ['name' => 'Sneaker Cleaning', 'description' => 'Hand clean and air dry sneakers.', 'price' => 3500, 'unit' => 'per_item', 'is_active' => true],
                    ['name' => 'Towel Bundle', 'description' => 'Bath and hand towel bundle.', 'price' => 2200, 'unit' => 'per_kg', 'is_active' => true],
                ],
            ],
        ];

        $cleaners = collect($cleanerProfiles)->map(function (array $profile): Cleaner {
            $user = User::query()->create([
                'name' => $profile['user']['name'],
                'email' => $profile['user']['email'],
                'password' => Hash::make('password'),
                'role' => 'cleaner',
                'phone' => $profile['user']['phone'],
                'address' => $profile['address'],
            ]);

            $cleaner = Cleaner::query()->create([
                'user_id' => $user->id,
                'business_name' => $profile['business_name'],
                'description' => $profile['description'],
                'address' => $profile['address'],
                'city' => $profile['city'],
                'phone' => $profile['phone'],
                'rating' => $profile['rating'],
                'turnaround_time' => $profile['turnaround_time'],
                'opening_hours' => $profile['opening_hours'],
                'is_available' => $profile['is_available'],
                'is_approved' => $profile['is_approved'],
            ]);

            collect($profile['services'])->each(fn (array $service) => Service::query()->create([
                'cleaner_id' => $cleaner->id,
                'name' => $service['name'],
                'description' => $service['description'],
                'price' => $service['price'],
                'unit' => $service['unit'],
                'is_active' => $service['is_active'],
            ]));

            return $cleaner;
        });

        $orders = [
            ['status' => 'pending', 'customer' => 0, 'cleaner' => 0, 'days_ago' => 1, 'delivery_fee' => 1500, 'platform_fee' => 700],
            ['status' => 'accepted', 'customer' => 1, 'cleaner' => 1, 'days_ago' => 2, 'delivery_fee' => 1000, 'platform_fee' => 600],
            ['status' => 'picked_up', 'customer' => 0, 'cleaner' => 2, 'days_ago' => 3, 'delivery_fee' => 1800, 'platform_fee' => 850],
            ['status' => 'in_cleaning', 'customer' => 1, 'cleaner' => 0, 'days_ago' => 4, 'delivery_fee' => 1200, 'platform_fee' => 650],
            ['status' => 'ready', 'customer' => 0, 'cleaner' => 1, 'days_ago' => 5, 'delivery_fee' => 1000, 'platform_fee' => 500],
            ['status' => 'out_for_delivery', 'customer' => 1, 'cleaner' => 2, 'days_ago' => 6, 'delivery_fee' => 1600, 'platform_fee' => 900],
            ['status' => 'completed', 'customer' => 0, 'cleaner' => 0, 'days_ago' => 12, 'delivery_fee' => 1200, 'platform_fee' => 800],
            ['status' => 'cancelled', 'customer' => 1, 'cleaner' => 1, 'days_ago' => 8, 'delivery_fee' => 1000, 'platform_fee' => 500],
        ];

        collect($orders)->each(function (array $orderData, int $index) use ($customers, $cleaners): void {
            $cleaner = $cleaners[$orderData['cleaner']];
            $customer = $customers[$orderData['customer']];
            $services = $cleaner->services()->take(2)->get();
            $createdAt = now()->subDays($orderData['days_ago']);
            $subtotal = 0;

            $order = Order::query()->create([
                'customer_id' => $customer->id,
                'cleaner_id' => $cleaner->id,
                'pickup_address' => $customer->address,
                'delivery_address' => $customer->address,
                'pickup_date' => $createdAt->copy()->addDay()->toDateString(),
                'pickup_time_window' => ['8am - 10am', '10am - 12pm', '12pm - 2pm'][$index % 3],
                'delivery_date' => $createdAt->copy()->addDays(3)->toDateString(),
                'delivery_time_window' => ['12pm - 2pm', '2pm - 4pm', '4pm - 6pm'][$index % 3],
                'status' => $orderData['status'],
                'delivery_fee' => $orderData['delivery_fee'],
                'platform_fee' => $orderData['platform_fee'],
                'pickup_notes' => 'Confirm pickup window with customer.',
                'delivery_notes' => 'Deliver to the saved customer address.',
                'notes' => $index % 2 === 0 ? 'Please call before pickup.' : 'Use gentle handling for delicate items.',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $services->each(function (Service $service, int $serviceIndex) use ($order, &$subtotal): void {
                $quantity = $serviceIndex + 1;
                $subtotal += (float) $service->price * $quantity;

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'service_id' => $service->id,
                    'quantity' => $quantity,
                    'price' => $service->price,
                ]);
            });

            if ($orderData['status'] === 'completed') {
                $paidAt = $createdAt->copy()->addDays(3);

                $order->update([
                    'subtotal' => $subtotal,
                    'total' => $subtotal + $orderData['delivery_fee'] + $orderData['platform_fee'],
                    'payment_status' => 'paid',
                    'paid_at' => $paidAt,
                ]);

                Payment::query()->create([
                    'order_id' => $order->id,
                    'customer_id' => $customer->id,
                    'amount' => $order->total,
                    'provider' => 'manual',
                    'reference' => 'LL-SEED-'.$order->id,
                    'status' => 'paid',
                    'paid_at' => $paidAt,
                    'metadata' => [
                        'seeded' => true,
                        'status' => 'success',
                    ],
                ]);

                Review::query()->create([
                    'order_id' => $order->id,
                    'customer_id' => $customer->id,
                    'cleaner_id' => $cleaner->id,
                    'rating' => 5,
                    'comment' => 'Great pickup timing and neat packaging.',
                ]);

                Review::refreshCleanerRating($cleaner);

                return;
            }

            $order->update([
                'subtotal' => $subtotal,
                'total' => $subtotal + $orderData['delivery_fee'] + $orderData['platform_fee'],
            ]);
        });
        DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
