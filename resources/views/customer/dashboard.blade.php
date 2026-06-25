<x-layouts.app title="Client Dashboard - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell py-12">
            <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Client dashboard</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Track laundry orders</h1>
            <p class="mt-4 max-w-2xl text-neutral-600">Track your laundry orders, active work, completed jobs, and recent cleaner activity.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('cleaners.index') }}" class="inline-flex rounded-md border border-neutral-300 bg-white px-5 py-3 text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Browse Cleaners</a>
                <a href="{{ route('orders.create') }}" class="inline-flex rounded-md bg-teal-700 px-5 py-3 text-sm font-bold text-white hover:bg-teal-800">Create New Order</a>
                <a href="{{ route('customer.addresses.index') }}" class="inline-flex rounded-md border border-neutral-300 bg-white px-5 py-3 text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Manage Addresses</a>
                <a href="{{ route('notifications.index') }}" class="inline-flex rounded-md border border-neutral-300 bg-white px-5 py-3 text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Notifications</a>
            </div>
        </div>
    </section>

    <section class="container-shell py-10">
        <div class="grid gap-4 md:grid-cols-3">
            <x-stat-card label="Total orders" :value="$stats['total_orders']" caption="Your submitted orders" />
            <x-stat-card label="Active orders" :value="$stats['active_orders']" caption="Not completed or cancelled" />
            <x-stat-card label="Completed orders" :value="$stats['completed_orders']" caption="Ready for review history" />
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-2">
            <section class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl font-black text-neutral-950">Unread notifications</h2>
                    <a href="{{ route('notifications.index') }}" class="text-sm font-bold text-teal-800 hover:text-teal-900">View all</a>
                </div>
                <div class="mt-5 divide-y divide-neutral-200">
                    @forelse ($unreadNotifications as $notification)
                        <div class="py-4 first:pt-0 last:pb-0">
                            <x-notification-type-badge :type="$notification->type" />
                            <p class="mt-2 font-bold text-neutral-950">{{ $notification->title }}</p>
                            <p class="mt-1 text-sm leading-6 text-neutral-600">{{ $notification->message }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-600">No unread notifications right now.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-black text-neutral-950">Latest activity</h2>
                <div class="mt-5 divide-y divide-neutral-200">
                    @forelse ($recentActivities as $activity)
                        <div class="py-4 first:pt-0 last:pb-0">
                            <p class="font-bold text-neutral-950">{{ str($activity->action)->replace('_', ' ')->title() }}</p>
                            <p class="mt-1 text-sm leading-6 text-neutral-600">{{ $activity->description }}</p>
                            <p class="mt-1 text-xs text-neutral-500">{{ $activity->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-600">Order activity will appear here as cleaners and payments update.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="mt-10 rounded-lg border border-neutral-200 bg-white shadow-sm">
            <div class="border-b border-neutral-200 p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <h2 class="text-xl font-black text-neutral-950">Orders</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($orderFilters as $filter)
                            <a href="{{ route('customer.dashboard', ['filter' => $filter]) }}" class="rounded-full px-3 py-2 text-xs font-bold {{ $currentFilter === $filter ? 'bg-teal-700 text-white' : 'bg-neutral-100 text-neutral-700 hover:bg-teal-50 hover:text-teal-800' }}">
                                {{ str($filter)->replace('_', ' ')->title() }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1040px] text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase tracking-[0.16em] text-neutral-500">
                        <tr>
                            <th class="px-5 py-3">Order</th>
                            <th class="px-5 py-3">Cleaner</th>
                            <th class="px-5 py-3">Pickup</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Payment</th>
                            <th class="px-5 py-3">Total</th>
                            <th class="px-5 py-3">Paid</th>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-5 py-4">
                                    <a href="{{ route('orders.show', $order) }}" class="font-bold text-neutral-950 hover:text-teal-800">#{{ str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) }}</a>
                                </td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('orders.show', $order) }}" class="font-bold text-neutral-950 hover:text-teal-800">
                                        {{ $order->cleaner?->business_name }}
                                    </a>
                                </td>
                                <td class="px-5 py-4 text-neutral-600">
                                    {{ $order->pickup_date?->format('M j') ?? 'Not set' }}
                                    @if ($order->pickup_time_window)
                                        <span class="block text-xs text-neutral-500">{{ $order->pickup_time_window }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4"><x-status-badge :status="$order->status" /></td>
                                <td class="px-5 py-4"><x-payment-status-badge :status="$order->payment_status" /></td>
                                <td class="px-5 py-4 font-semibold">NGN {{ number_format((float) $order->total, 2) }}</td>
                                <td class="px-5 py-4 text-neutral-600">{{ $order->paid_at?->format('M j, Y') ?? '-' }}</td>
                                <td class="px-5 py-4 text-neutral-600">{{ $order->created_at->format('M j, Y') }}</td>
                                <td class="px-5 py-4">
                                    @if (in_array($order->payment_status, ['unpaid', 'failed'], true))
                                        <span class="text-xs font-bold text-neutral-700">Pay offline</span>
                                    @elseif ($order->isReviewableBy(auth()->user()))
                                        <a href="{{ route('orders.review.create', $order) }}" class="rounded-md bg-amber-500 px-3 py-2 text-xs font-bold text-white hover:bg-amber-600">Leave Review</a>
                                    @elseif ($order->review)
                                        <span class="text-xs font-bold text-amber-700">Reviewed</span>
                                    @elseif ($order->payment_status === 'pending')
                                        <span class="text-xs font-bold text-sky-700">Awaiting confirmation</span>
                                    @else
                                        <span class="text-xs font-bold text-teal-700">Paid</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-8 text-neutral-600">
                                    No orders match this filter. <a href="{{ route('orders.create') }}" class="font-bold text-teal-800">Create an order</a> or try another filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-black text-neutral-950">Ready for feedback</h2>
            <div class="mt-5 divide-y divide-neutral-200">
                @forelse ($reviewableOrders as $order)
                    <div class="flex flex-col gap-3 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-bold text-neutral-950">Order #{{ str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) }}</p>
                            <p class="mt-1 text-sm text-neutral-600">{{ $order->cleaner?->business_name }} - completed {{ $order->updated_at->format('M j, Y') }}</p>
                        </div>
                        <a href="{{ route('orders.review.create', $order) }}" class="rounded-md bg-amber-500 px-4 py-2 text-center text-sm font-bold text-white hover:bg-amber-600">Leave Review</a>
                    </div>
                @empty
                    <p class="text-sm text-neutral-600">No eligible orders to review yet. Completed and paid orders will appear here.</p>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.app>
