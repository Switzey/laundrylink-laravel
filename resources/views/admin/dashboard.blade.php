<x-layouts.app title="Admin Dashboard - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell py-12">
            <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Admin dashboard</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Platform overview</h1>
            <p class="mt-4 max-w-2xl text-neutral-600">Monitor users, cleaner approvals, completed work, and recent marketplace order activity.</p>
        </div>
    </section>

    <section class="container-shell space-y-10 py-10">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.logistics') }}" class="rounded-md bg-teal-700 px-5 py-3 text-sm font-bold text-white hover:bg-teal-800">View Logistics Overview</a>
            <a href="{{ route('admin.reports') }}" class="rounded-md border border-neutral-300 bg-white px-5 py-3 text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Platform Reports</a>
            <a href="{{ route('admin.reviews.index') }}" class="rounded-md border border-neutral-300 bg-white px-5 py-3 text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Review Moderation</a>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Total users" :value="$stats['total_users']" />
            <x-stat-card label="Customers" :value="$stats['total_customers']" />
            <x-stat-card label="Cleaner profiles" :value="$stats['total_cleaners']" />
            <x-stat-card label="Approved cleaners" :value="$stats['approved_cleaners']" />
            <x-stat-card label="Pending approvals" :value="$stats['pending_cleaners']" />
            <x-stat-card label="Total orders" :value="$stats['total_orders']" />
            <x-stat-card label="Pending orders" :value="$stats['pending_orders']" />
            <x-stat-card label="Completed orders" :value="$stats['completed_orders']" />
            <x-stat-card label="Cancelled orders" :value="$stats['cancelled_orders']" />
            <x-stat-card label="Paid orders" :value="$stats['paid_orders']" />
            <x-stat-card label="Unpaid orders" :value="$stats['unpaid_orders']" />
            <x-stat-card label="Paid revenue" :value="'NGN '.number_format((float) $stats['total_revenue'], 2)" />
            <x-stat-card label="Platform fees" :value="'NGN '.number_format((float) $stats['total_platform_fees'], 2)" />
            <x-stat-card label="Total reviews" :value="$stats['total_reviews']" />
            <x-stat-card label="Avg rating" :value="number_format((float) $stats['average_platform_rating'], 1).'/5'" />
        </div>

        <div class="grid gap-8 xl:grid-cols-2">
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
                <h2 class="text-xl font-black text-neutral-950">Recent platform activity</h2>
                <div class="mt-5 divide-y divide-neutral-200">
                    @forelse ($recentActivities as $activity)
                        <div class="py-4 first:pt-0 last:pb-0">
                            <p class="font-bold text-neutral-950">{{ str($activity->action)->replace('_', ' ')->title() }}</p>
                            <p class="mt-1 text-sm leading-6 text-neutral-600">{{ $activity->description }}</p>
                            <p class="mt-1 text-xs text-neutral-500">
                                Order #{{ str_pad((string) $activity->order_id, 4, '0', STR_PAD_LEFT) }}
                                @if ($activity->order?->cleaner)
                                    - {{ $activity->order->cleaner->business_name }}
                                @endif
                                - {{ $activity->created_at->format('M j, Y g:i A') }}
                            </p>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-600">No order activity has been logged yet.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="grid gap-8 xl:grid-cols-2">
            <section class="rounded-lg border border-neutral-200 bg-white shadow-sm">
                <div class="border-b border-neutral-200 p-5">
                    <h2 class="text-xl font-black text-neutral-950">Pending cleaner approvals</h2>
                </div>
                <div class="divide-y divide-neutral-200">
                    @forelse ($pendingCleaners as $cleaner)
                        <div class="p-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="font-black text-neutral-950">{{ $cleaner->business_name }}</p>
                                    <p class="mt-1 text-sm text-neutral-600">{{ $cleaner->city }} - {{ $cleaner->phone }}</p>
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-neutral-500">
                                        <span>{{ $cleaner->user?->email }}</span>
                                        <span>{{ $cleaner->services_count }} services</span>
                                        <x-availability-status-badge :available="$cleaner->is_available" />
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('admin.cleaners.approve', $cleaner) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-md bg-teal-700 px-3 py-2 text-xs font-bold text-white hover:bg-teal-800">Approve</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-neutral-600">No cleaners are waiting for approval.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-neutral-200 bg-white shadow-sm">
                <div class="border-b border-neutral-200 p-5">
                    <h2 class="text-xl font-black text-neutral-950">Approved cleaners</h2>
                </div>
                <div class="divide-y divide-neutral-200">
                    @forelse ($approvedCleaners as $cleaner)
                        <div class="p-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="font-black text-neutral-950">{{ $cleaner->business_name }}</p>
                                    <p class="mt-1 text-sm text-neutral-600">{{ $cleaner->city }} - {{ $cleaner->turnaround_time ?? 'Flexible' }}</p>
                                    <div class="mt-2 flex flex-wrap items-center gap-2">
                                        <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-bold text-neutral-700">{{ $cleaner->services_count }} services</span>
                                        <x-availability-status-badge :available="$cleaner->is_available" />
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('admin.cleaners.unapprove', $cleaner) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-md border border-rose-300 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-50">Unapprove</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-neutral-600">No approved cleaners yet.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="rounded-lg border border-neutral-200 bg-white shadow-sm">
            <div class="border-b border-neutral-200 p-5">
                <h2 class="text-xl font-black text-neutral-950">Recent orders</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase tracking-[0.16em] text-neutral-500">
                        <tr>
                            <th class="px-5 py-3">Order</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Cleaner</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Total</th>
                            <th class="px-5 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                        @forelse ($recentOrders as $order)
                            <tr>
                                <td class="px-5 py-4">
                                    <a href="{{ route('orders.show', $order) }}" class="font-bold text-neutral-950 hover:text-teal-800">#{{ str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) }}</a>
                                </td>
                                <td class="px-5 py-4 text-neutral-600">{{ $order->customer?->name ?? 'Guest' }}</td>
                                <td class="px-5 py-4 text-neutral-600">{{ $order->cleaner?->business_name }}</td>
                                <td class="px-5 py-4"><x-status-badge :status="$order->status" /></td>
                                <td class="px-5 py-4 font-semibold">NGN {{ number_format((float) $order->total, 2) }}</td>
                                <td class="px-5 py-4 text-neutral-600">{{ $order->created_at->format('M j, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-neutral-600">No recent orders yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-lg border border-neutral-200 bg-white shadow-sm">
            <div class="border-b border-neutral-200 p-5">
                <h2 class="text-xl font-black text-neutral-950">Recent payments</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase tracking-[0.16em] text-neutral-500">
                        <tr>
                            <th class="px-5 py-3">Reference</th>
                            <th class="px-5 py-3">Order</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Cleaner</th>
                            <th class="px-5 py-3">Amount</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                        @forelse ($recentPayments as $payment)
                            <tr>
                                <td class="max-w-[220px] break-all px-5 py-4 font-semibold text-neutral-900">{{ $payment->reference ?? 'No reference yet' }}</td>
                                <td class="px-5 py-4">
                                    @if ($payment->order)
                                        <a href="{{ route('orders.show', $payment->order) }}" class="font-bold text-neutral-950 hover:text-teal-800">#{{ str_pad((string) $payment->order->id, 4, '0', STR_PAD_LEFT) }}</a>
                                    @else
                                        <span class="text-neutral-500">Missing order</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-neutral-600">{{ $payment->customer?->name ?? 'Guest' }}</td>
                                <td class="px-5 py-4 text-neutral-600">{{ $payment->order?->cleaner?->business_name ?? '-' }}</td>
                                <td class="px-5 py-4 font-semibold">NGN {{ number_format((float) $payment->amount, 2) }}</td>
                                <td class="px-5 py-4"><x-payment-status-badge :status="$payment->status" /></td>
                                <td class="px-5 py-4 text-neutral-600">{{ $payment->created_at->format('M j, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-neutral-600">No payments have been started yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-lg border border-neutral-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-neutral-200 p-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-xl font-black text-neutral-950">Recent reviews</h2>
                <a href="{{ route('admin.reviews.index') }}" class="text-sm font-bold text-teal-800 hover:text-teal-900">View all reviews</a>
            </div>
            <div class="divide-y divide-neutral-200">
                @forelse ($recentReviews as $review)
                    <div class="p-5">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="font-bold text-neutral-950">{{ $review->cleaner?->business_name }}</p>
                                <p class="mt-1 text-sm text-neutral-600">{{ $review->customer?->name ?? 'LaundryLink customer' }} - Order #{{ str_pad((string) $review->order_id, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <x-rating-stars :rating="$review->rating" />
                        </div>
                        @if ($review->comment)
                            <p class="mt-3 text-sm leading-6 text-neutral-600">{{ $review->comment }}</p>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-neutral-600">No customer reviews yet.</div>
                @endforelse
            </div>
        </section>
    </section>
</x-layouts.app>
