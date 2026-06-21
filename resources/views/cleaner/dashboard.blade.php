<x-layouts.app title="Cleaner Dashboard - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell py-12">
            <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Cleaner dashboard</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">{{ $cleaner?->business_name ?? 'Cleaner workspace' }}</h1>
            <p class="mt-4 max-w-2xl text-neutral-600">Incoming orders, recent work, and operational action buttons for the first cleaner in the seeded data.</p>
        </div>
    </section>

    <section class="container-shell grid gap-8 py-10 lg:grid-cols-[1fr_380px]">
        <div class="space-y-8">
            <div class="rounded-lg border border-neutral-200 bg-white shadow-sm">
                <div class="border-b border-neutral-200 p-5">
                    <h2 class="text-xl font-black text-neutral-950">Incoming orders</h2>
                </div>
                <div class="divide-y divide-neutral-200">
                    @forelse ($incomingOrders as $order)
                        <div class="p-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="font-black text-neutral-950">Order #{{ str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    <p class="mt-1 text-sm text-neutral-600">{{ $order->customer?->name }} - NGN {{ number_format((float) $order->total, 2) }}</p>
                                </div>
                                <x-status-badge :status="$order->status" />
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <button type="button" class="rounded-md bg-teal-700 px-3 py-2 text-xs font-bold text-white hover:bg-teal-800">Accept</button>
                                <button type="button" class="rounded-md border border-neutral-300 px-3 py-2 text-xs font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Mark Picked Up</button>
                                <button type="button" class="rounded-md border border-neutral-300 px-3 py-2 text-xs font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Mark Ready</button>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-neutral-600">No incoming orders yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-neutral-200 bg-white shadow-sm">
                <div class="border-b border-neutral-200 p-5">
                    <h2 class="text-xl font-black text-neutral-950">Recent orders</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[680px] text-left text-sm">
                        <thead class="bg-neutral-50 text-xs uppercase tracking-[0.16em] text-neutral-500">
                            <tr>
                                <th class="px-5 py-3">Customer</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Pickup</th>
                                <th class="px-5 py-3">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200">
                            @forelse ($recentOrders as $order)
                                <tr>
                                    <td class="px-5 py-4 font-bold text-neutral-950">{{ $order->customer?->name }}</td>
                                    <td class="px-5 py-4"><x-status-badge :status="$order->status" /></td>
                                    <td class="px-5 py-4 text-neutral-600">{{ $order->pickup_date?->format('M j, Y') ?? 'Not set' }}</td>
                                    <td class="px-5 py-4 font-semibold">NGN {{ number_format((float) $order->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-neutral-600">No recent orders yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <aside class="h-fit rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-black text-neutral-950">Cleaner snapshot</h2>
            @if ($cleaner)
                <dl class="mt-5 space-y-4 text-sm">
                    <div>
                        <dt class="text-neutral-500">Rating</dt>
                        <dd class="font-bold text-neutral-950">{{ number_format((float) $cleaner->rating, 1) }}/5</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Turnaround</dt>
                        <dd class="font-bold text-neutral-950">{{ $cleaner->turnaround_time }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Services</dt>
                        <dd class="font-bold text-neutral-950">{{ $cleaner->services->count() }}</dd>
                    </div>
                </dl>
            @else
                <p class="mt-4 text-sm text-neutral-600">Seed the database to load a cleaner profile.</p>
            @endif
        </aside>
    </section>
</x-layouts.app>
