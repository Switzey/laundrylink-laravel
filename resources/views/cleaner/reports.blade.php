<x-layouts.app title="Vendor Reports - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell flex flex-col gap-4 py-12 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Vendor reports</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">{{ $cleaner?->business_name ?? 'Reports' }}</h1>
                <p class="mt-4 max-w-2xl text-neutral-600">Track order volume, payment activity, service demand, and recent customer feedback.</p>
            </div>
            <a href="{{ route('vendor.dashboard') }}" class="rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Back to Dashboard</a>
        </div>
    </section>

    <section class="container-shell space-y-8 py-10">
        @if (! $cleaner)
            <div class="rounded-lg border border-dashed border-neutral-300 bg-white p-8 text-neutral-600">
                Create your cleaner profile before reporting data can be shown.
            </div>
        @else
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <x-stat-card label="Total orders" :value="$stats['total_orders']" />
                <x-stat-card label="Pending orders" :value="$stats['pending_orders']" />
                <x-stat-card label="Active orders" :value="$stats['active_orders']" />
                <x-stat-card label="Completed orders" :value="$stats['completed_orders']" />
                <x-stat-card label="Cancelled orders" :value="$stats['cancelled_orders']" />
                <x-stat-card label="Paid orders" :value="$stats['paid_orders']" />
                <x-stat-card label="Estimated earnings" :value="'NGN '.number_format((float) $stats['estimated_earnings'], 2)" />
            </div>

            <div class="grid gap-8 xl:grid-cols-2">
                <section class="rounded-lg border border-neutral-200 bg-white shadow-sm">
                    <div class="border-b border-neutral-200 p-5">
                        <h2 class="text-xl font-black text-neutral-950">Recent payments</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[660px] text-left text-sm">
                            <thead class="bg-neutral-50 text-xs uppercase tracking-[0.16em] text-neutral-500">
                                <tr>
                                    <th class="px-5 py-3">Reference</th>
                                    <th class="px-5 py-3">Order</th>
                                    <th class="px-5 py-3">Amount</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200">
                                @forelse ($recentPayments as $payment)
                                    <tr>
                                        <td class="max-w-[180px] break-all px-5 py-4 font-semibold text-neutral-900">{{ $payment->reference ?? 'No reference yet' }}</td>
                                        <td class="px-5 py-4"><a href="{{ route('orders.show', $payment->order) }}" class="font-bold text-neutral-950 hover:text-teal-800">#{{ str_pad((string) $payment->order_id, 4, '0', STR_PAD_LEFT) }}</a></td>
                                        <td class="px-5 py-4 font-semibold">NGN {{ number_format((float) $payment->amount, 2) }}</td>
                                        <td class="px-5 py-4"><x-payment-status-badge :status="$payment->status" /></td>
                                        <td class="px-5 py-4 text-neutral-600">{{ $payment->created_at->format('M j, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-5 py-8 text-neutral-600">No payments have been recorded for your orders yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-black text-neutral-950">Most ordered services</h2>
                    <div class="mt-5 divide-y divide-neutral-200">
                        @forelse ($mostOrderedServices as $service)
                            <div class="flex items-start justify-between gap-4 py-3 first:pt-0 last:pb-0">
                                <div>
                                    <p class="font-bold text-neutral-950">{{ $service->name }}</p>
                                    <p class="mt-1 text-sm text-neutral-600">NGN {{ number_format((float) $service->price, 2) }} / {{ str($service->unit)->replace('_', ' ') }}</p>
                                </div>
                                <span class="rounded-full bg-teal-100 px-3 py-1 text-xs font-bold text-teal-800">{{ (int) $service->ordered_quantity }} ordered</span>
                            </div>
                        @empty
                            <p class="text-sm text-neutral-600">Service demand will appear once orders include your services.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <section class="rounded-lg border border-neutral-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-neutral-200 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-xl font-black text-neutral-950">Recent reviews</h2>
                    <x-rating-stars :rating="$cleaner->rating" :count="$cleaner->reviews()->count()" />
                </div>
                <div class="divide-y divide-neutral-200">
                    @forelse ($recentReviews as $review)
                        <article class="p-5">
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
                        </article>
                    @empty
                        <div class="p-8 text-neutral-600">No customer reviews yet.</div>
                    @endforelse
                </div>
            </section>
        @endif
    </section>
</x-layouts.app>
