<x-layouts.app title="Vendor Dashboard - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell py-12">
            <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Vendor dashboard</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">{{ $cleaner?->business_name ?? 'Cleaner workspace' }}</h1>
            <p class="mt-4 max-w-2xl text-neutral-600">Manage cleaner profile approval, incoming orders, active work, and completed laundry jobs.</p>
        </div>
    </section>

    <section class="container-shell grid gap-8 py-10 lg:grid-cols-[1fr_380px]">
        <div class="space-y-8">
            @if (! $cleaner)
                <section class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-black text-neutral-950">Create cleaner profile</h2>
                    <p class="mt-2 text-sm text-neutral-600">Complete your business profile so an admin can approve you for customer orders.</p>
                    <a href="{{ route('cleaner.profile.edit') }}" class="mt-5 inline-flex rounded-md bg-teal-700 px-5 py-3 text-sm font-bold text-white hover:bg-teal-800">Set Up Profile</a>
                </section>
            @else
                <section class="rounded-lg border {{ $cleaner->is_approved ? 'border-teal-200 bg-teal-50' : 'border-amber-200 bg-amber-50' }} p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-xl font-black {{ $cleaner->is_approved ? 'text-teal-950' : 'text-amber-950' }}">
                                {{ $cleaner->is_approved ? 'Approved cleaner profile' : 'Pending approval' }}
                            </h2>
                            <p class="mt-2 text-sm {{ $cleaner->is_approved ? 'text-teal-900' : 'text-amber-900' }}">
                                {{ $cleaner->is_approved ? 'Customers can create orders with your business when you are available.' : 'Customers may not see your business until an admin approves your profile.' }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <x-availability-status-badge :available="$cleaner->is_available" />
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $cleaner->is_approved ? 'bg-teal-100 text-teal-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $cleaner->is_approved ? 'Approved' : 'Not approved' }}
                            </span>
                        </div>
                    </div>
                </section>

                @if (! $cleaner->is_available)
                    <section class="rounded-lg border border-rose-200 bg-rose-50 p-5 text-sm font-semibold text-rose-900">
                        Your business is currently unavailable, so customers cannot start new orders with you.
                    </section>
                @endif

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                    <x-stat-card label="Total services" :value="$stats['total_services']" />
                    <x-stat-card label="Active services" :value="$stats['active_services']" />
                    <x-stat-card label="Pending orders" :value="$stats['pending_orders']" />
                    <x-stat-card label="Completed orders" :value="$stats['completed_orders']" />
                    <x-stat-card label="Earnings estimate" :value="'NGN '.number_format((float) $stats['earnings_estimate'], 2)" />
                    <x-stat-card label="Reviews" :value="$stats['total_reviews']" />
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('cleaner.profile.edit') }}" class="rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Edit Profile</a>
                    <a href="{{ route('cleaner.services.index') }}" class="rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Manage Services</a>
                    <a href="{{ route('vendor.schedule') }}" class="rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">View Schedule</a>
                    <a href="{{ route('vendor.reports') }}" class="rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Reports</a>
                    <a href="#orders" class="rounded-md bg-teal-700 px-4 py-2 text-sm font-bold text-white hover:bg-teal-800">View Orders</a>
                </div>

                <div class="grid gap-8 lg:grid-cols-2">
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
                        <h2 class="text-xl font-black text-neutral-950">Latest order activity</h2>
                        <div class="mt-5 divide-y divide-neutral-200">
                            @forelse ($recentActivities as $activity)
                                <div class="py-4 first:pt-0 last:pb-0">
                                    <p class="font-bold text-neutral-950">{{ str($activity->action)->replace('_', ' ')->title() }}</p>
                                    <p class="mt-1 text-sm leading-6 text-neutral-600">{{ $activity->description }}</p>
                                    <p class="mt-1 text-xs text-neutral-500">{{ $activity->created_at->format('M j, Y g:i A') }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-neutral-600">Order activity will appear here as work moves forward.</p>
                            @endforelse
                        </div>
                    </section>
                </div>

                <div id="orders" class="rounded-lg border border-neutral-200 bg-white shadow-sm">
                    <div class="border-b border-neutral-200 p-5">
                        <h2 class="text-xl font-black text-neutral-950">Incoming orders</h2>
                    </div>
                    <div class="divide-y divide-neutral-200">
                        @forelse ($incomingOrders as $order)
                            @include('cleaner.partials.order-row', ['order' => $order, 'compact' => false])
                        @empty
                            <div class="p-8 text-neutral-600">No incoming orders right now.</div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-lg border border-neutral-200 bg-white shadow-sm">
                    <div class="border-b border-neutral-200 p-5">
                        <h2 class="text-xl font-black text-neutral-950">Recent orders</h2>
                    </div>
                    <div class="divide-y divide-neutral-200">
                        @forelse ($recentOrders as $order)
                            @include('cleaner.partials.order-row', ['order' => $order, 'compact' => true])
                        @empty
                            <div class="p-8 text-neutral-600">No recent orders yet.</div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-lg border border-neutral-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-3 border-b border-neutral-200 p-5 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="text-xl font-black text-neutral-950">Customer reviews</h2>
                        <x-rating-stars :rating="$cleaner->rating" :count="$stats['total_reviews']" />
                    </div>
                    <div class="divide-y divide-neutral-200">
                        @forelse ($recentReviews as $review)
                            <div class="p-5">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="font-bold text-neutral-950">{{ $review->customer?->name ?? 'LaundryLink customer' }}</p>
                                        <p class="mt-1 text-xs text-neutral-500">Order #{{ str_pad((string) $review->order_id, 4, '0', STR_PAD_LEFT) }} - {{ $review->created_at->format('M j, Y') }}</p>
                                    </div>
                                    <x-rating-stars :rating="$review->rating" />
                                </div>
                                @if ($review->comment)
                                    <p class="mt-3 text-sm leading-6 text-neutral-600">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="p-8 text-neutral-600">No reviews yet. Completed paid orders can be reviewed by customers.</div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <aside class="h-fit rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-black text-neutral-950">Cleaner profile</h2>
            @if ($cleaner)
                <dl class="mt-5 space-y-4 text-sm">
                    <div>
                        <dt class="text-neutral-500">Approval</dt>
                        <dd class="font-bold text-neutral-950">{{ $cleaner->is_approved ? 'Approved' : 'Pending approval' }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Availability</dt>
                        <dd class="mt-1"><x-availability-status-badge :available="$cleaner->is_available" /></dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Rating</dt>
                        <dd class="mt-1"><x-rating-stars :rating="$cleaner->rating" :count="$stats['total_reviews']" /></dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Turnaround</dt>
                        <dd class="font-bold text-neutral-950">{{ $cleaner->turnaround_time ?? 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Services</dt>
                        <dd class="font-bold text-neutral-950">{{ $stats['active_services'] }} active of {{ $stats['total_services'] }}</dd>
                    </div>
                </dl>

                <div class="mt-6 grid gap-2 border-t border-neutral-200 pt-5">
                    <a href="{{ route('cleaner.profile.edit') }}" class="rounded-md border border-neutral-300 px-4 py-2 text-center text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Edit Profile</a>
                    <a href="{{ route('vendor.schedule') }}" class="rounded-md border border-neutral-300 px-4 py-2 text-center text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">View Schedule</a>
                    <a href="{{ route('vendor.reports') }}" class="rounded-md border border-neutral-300 px-4 py-2 text-center text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Reports</a>
                    <a href="{{ route('cleaner.services.index') }}" class="rounded-md bg-teal-700 px-4 py-2 text-center text-sm font-bold text-white hover:bg-teal-800">Manage Services</a>
                </div>
            @else
                <p class="mt-4 text-sm text-neutral-600">Create your profile to begin onboarding.</p>
                <a href="{{ route('cleaner.profile.edit') }}" class="mt-5 inline-flex rounded-md bg-teal-700 px-4 py-2 text-sm font-bold text-white hover:bg-teal-800">Set Up Profile</a>
            @endif
        </aside>
    </section>
</x-layouts.app>
