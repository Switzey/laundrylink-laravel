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
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->create([
            'name' => 'Ada Admin',
            'email' => 'admin@laundrylink.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '08010000001',
            'address' => '12 Marina Road, Lagos',
        ]);

        $customers = collect([
            [
                'name' => 'Tola Martins',
                'email' => 'tola@example.com',
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
            ]);
        });

        $cleanerProfiles = [
            [
                'user' => ['name' => 'Bisi Fresh', 'email' => 'bisi@freshfold.test', 'phone' => '08030000001'],
                'business_name' => 'FreshFold Laundry',
                'description' => 'Neighborhood wash, fold, and dry-cleaning with careful packaging for busy homes.',
                'address' => '8 Admiralty Road',
                'city' => 'Lagos',
                'phone' => '08030000001',
                'rating' => 4.8,
                'turnaround_time' => '24-48 hours',
                'is_approved' => true,
                'services' => [
                    ['name' => 'Shirt Laundry', 'description' => 'Washed, pressed, and folded shirts.', 'price' => 1200, 'unit' => 'per_item'],
                    ['name' => 'Suit Dry Cleaning', 'description' => 'Two-piece suit care with finishing.', 'price' => 6500, 'unit' => 'per_item'],
                    ['name' => 'Wash & Fold', 'description' => 'Everyday clothing by weight.', 'price' => 1800, 'unit' => 'per_kg'],
                    ['name' => 'Bedding Set', 'description' => 'Sheets, pillowcases, and duvet cover.', 'price' => 5000, 'unit' => 'flat'],
                ],
            ],
            [
                'user' => ['name' => 'Ife Clean', 'email' => 'ife@quickpress.test', 'phone' => '08030000002'],
                'business_name' => 'QuickPress Cleaners',
                'description' => 'Fast pressing, stain treatment, and delivery slots for office wear.',
                'address' => '31 Allen Avenue',
                'city' => 'Ikeja',
                'phone' => '08030000002',
                'rating' => 4.6,
                'turnaround_time' => 'Same day',
                'is_approved' => true,
                'services' => [
                    ['name' => 'Office Shirt', 'description' => 'Crisp shirt laundry and press.', 'price' => 1000, 'unit' => 'per_item'],
                    ['name' => 'Trouser Press', 'description' => 'Steam press and fold.', 'price' => 900, 'unit' => 'per_item'],
                    ['name' => 'Agbada Care', 'description' => 'Special care for traditional outfits.', 'price' => 7500, 'unit' => 'per_item'],
                    ['name' => 'Express Wash', 'description' => 'Priority wash bundle.', 'price' => 4000, 'unit' => 'flat'],
                ],
            ],
            [
                'user' => ['name' => 'Musa Sparkle', 'email' => 'musa@sparkle.test', 'phone' => '08030000003'],
                'business_name' => 'SparkleCare Drycleaners',
                'description' => 'Premium fabric care for delicate garments, gowns, curtains, and linens.',
                'address' => '16 Gana Street',
                'city' => 'Abuja',
                'phone' => '08030000003',
                'rating' => 4.9,
                'turnaround_time' => '48 hours',
                'is_approved' => true,
                'services' => [
                    ['name' => 'Dress Dry Cleaning', 'description' => 'Gentle dry cleaning for dresses.', 'price' => 4500, 'unit' => 'per_item'],
                    ['name' => 'Curtain Cleaning', 'description' => 'Deep clean for curtains by weight.', 'price' => 2500, 'unit' => 'per_kg'],
                    ['name' => 'Delicate Fabric Care', 'description' => 'Silk, lace, and special garments.', 'price' => 6000, 'unit' => 'per_item'],
                    ['name' => 'Duvet Cleaning', 'description' => 'Large duvet wash and dry.', 'price' => 7000, 'unit' => 'flat'],
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
                'is_approved' => false,
                'services' => [
                    ['name' => 'Family Wash Bag', 'description' => 'Mixed family laundry package.', 'price' => 9500, 'unit' => 'flat'],
                    ['name' => 'Children Wear', 'description' => 'Gentle wash for kids clothing.', 'price' => 800, 'unit' => 'per_item'],
                    ['name' => 'Sneaker Cleaning', 'description' => 'Hand clean and air dry sneakers.', 'price' => 3500, 'unit' => 'per_item'],
                    ['name' => 'Towel Bundle', 'description' => 'Bath and hand towel bundle.', 'price' => 2200, 'unit' => 'per_kg'],
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
                'is_approved' => $profile['is_approved'],
            ]);

            collect($profile['services'])->each(fn (array $service) => Service::query()->create([
                'cleaner_id' => $cleaner->id,
                'name' => $service['name'],
                'description' => $service['description'],
                'price' => $service['price'],
                'unit' => $service['unit'],
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
                'delivery_date' => $createdAt->copy()->addDays(3)->toDateString(),
                'status' => $orderData['status'],
                'delivery_fee' => $orderData['delivery_fee'],
                'platform_fee' => $orderData['platform_fee'],
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

            $order->update([
                'subtotal' => $subtotal,
                'total' => $subtotal + $orderData['delivery_fee'] + $orderData['platform_fee'],
            ]);

            Payment::query()->create([
                'order_id' => $order->id,
                'amount' => $order->total,
                'provider' => null,
                'reference' => null,
                'status' => $orderData['status'] === 'completed' ? 'paid' : 'pending',
            ]);

            if ($orderData['status'] === 'completed') {
                Review::query()->create([
                    'order_id' => $order->id,
                    'customer_id' => $customer->id,
                    'cleaner_id' => $cleaner->id,
                    'rating' => 5,
                    'comment' => 'Great pickup timing and neat packaging.',
                ]);
            }
        });
    }
}
