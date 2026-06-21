<x-layouts.app title="LaundryLink">
    <section class="bg-white">
        <div class="container-shell grid gap-10 py-16 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:py-24">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Laundry pickup and dry-cleaning booking</p>
                <h1 class="mt-5 max-w-3xl text-4xl font-black tracking-tight text-neutral-950 sm:text-6xl">
                    Book trusted cleaners without losing your Saturday.
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-neutral-600">
                    LaundryLink connects customers to vetted laundry and dry-cleaning businesses for pickup, service selection, delivery, and simple order tracking.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('cleaners.index') }}" class="inline-flex items-center justify-center rounded-md bg-teal-700 px-6 py-3 text-sm font-bold text-white hover:bg-teal-800">Browse Cleaners</a>
                    <a href="{{ route('orders.create') }}" class="inline-flex items-center justify-center rounded-md border border-neutral-300 bg-white px-6 py-3 text-sm font-bold text-neutral-900 hover:border-teal-700 hover:text-teal-800">Start an Order</a>
                </div>
            </div>

            <div class="rounded-lg bg-gradient-to-br from-teal-700 via-teal-600 to-emerald-500 p-1 shadow-xl">
                <div class="rounded-md bg-white p-6">
                    <div class="flex items-center justify-between border-b border-neutral-200 pb-4">
                        <div>
                            <p class="text-sm font-semibold text-neutral-500">Order #LL-1042</p>
                            <h2 class="text-2xl font-black text-neutral-950">Dry cleaning pickup</h2>
                        </div>
                        <x-status-badge status="picked_up" />
                    </div>
                    <div class="mt-6 space-y-4">
                        <div class="rounded-lg bg-teal-50 p-4">
                            <p class="text-sm font-bold text-teal-900">FreshFold Laundry</p>
                            <p class="mt-1 text-sm text-teal-800">Pickup confirmed for today. Delivery expected in 48 hours.</p>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-md bg-neutral-50 p-3">
                                <p class="text-xs text-neutral-500">Pickup</p>
                                <p class="font-bold">Today</p>
                            </div>
                            <div class="rounded-md bg-neutral-50 p-3">
                                <p class="text-xs text-neutral-500">Items</p>
                                <p class="font-bold">8</p>
                            </div>
                            <div class="rounded-md bg-neutral-50 p-3">
                                <p class="text-xs text-neutral-500">Total</p>
                                <p class="font-bold">NGN 12,400</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="border-y border-neutral-200 bg-stone-100">
        <div class="container-shell grid gap-4 py-12 md:grid-cols-3">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="text-lg font-black">Pickup & Delivery</h3>
                <p class="mt-3 text-sm leading-6 text-neutral-600">Schedule pickup and delivery details from one clean order screen.</p>
            </div>
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="text-lg font-black">Trusted Cleaners</h3>
                <p class="mt-3 text-sm leading-6 text-neutral-600">Browse approved laundry businesses with pricing, city, rating, and turnaround time.</p>
            </div>
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="text-lg font-black">Easy Tracking</h3>
                <p class="mt-3 text-sm leading-6 text-neutral-600">Follow status changes from pending to pickup, cleaning, delivery, and completion.</p>
            </div>
        </div>
    </section>

    <section class="container-shell py-14">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Featured cleaners</p>
                <h2 class="mt-2 text-3xl font-black text-neutral-950">Start with a cleaner customers can trust.</h2>
            </div>
            <a href="{{ route('cleaners.index') }}" class="text-sm font-bold text-teal-800 hover:text-teal-900">See all cleaners</a>
        </div>

        <div class="mt-8 grid gap-5 md:grid-cols-3">
            @forelse ($featuredCleaners as $cleaner)
                <x-cleaner-card :cleaner="$cleaner" />
            @empty
                <div class="rounded-lg border border-dashed border-neutral-300 bg-white p-8 text-neutral-600 md:col-span-3">
                    Run the database seeder to show featured cleaners here.
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.app>
