<x-layouts.app title="Order #{{ $order->id }} - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell flex flex-col gap-4 py-12 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Order details</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Order #{{ str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) }}</h1>
                <p class="mt-4 text-neutral-600">{{ $order->cleaner?->business_name }} - created {{ $order->created_at->format('M j, Y') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <x-status-badge :status="$order->status" />
                <x-payment-status-badge :status="$order->payment_status" />
            </div>
        </div>
    </section>

    <section class="container-shell grid gap-8 py-10 lg:grid-cols-[1fr_360px]">
        <div class="space-y-8">
            <div class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-black text-neutral-950">Services</h2>
                <div class="mt-5 divide-y divide-neutral-200">
                    @foreach ($order->items as $item)
                        <div class="flex items-start justify-between gap-4 py-4 first:pt-0 last:pb-0">
                            <div>
                                <p class="font-bold text-neutral-950">{{ $item->service?->name }}</p>
                                <p class="mt-1 text-sm text-neutral-600">Quantity: {{ $item->quantity }}</p>
                            </div>
                            <p class="font-black text-teal-800">NGN {{ number_format((float) $item->price * $item->quantity, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <h2 class="text-xl font-black text-neutral-950">Pickup and delivery</h2>
                    @if (auth()->user()?->role === 'customer' && $order->customer_id === auth()->id() && ! in_array($order->status, ['completed', 'cancelled'], true))
                        <a href="{{ route('orders.reschedule.edit', $order) }}" class="rounded-md border border-neutral-300 px-3 py-2 text-xs font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Reschedule</a>
                    @endif
                </div>
                <dl class="mt-5 grid gap-4 md:grid-cols-2">
                    <div>
                        <dt class="text-sm text-neutral-500">Pickup address</dt>
                        <dd class="mt-1 font-bold text-neutral-950">{{ $order->pickup_address }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-neutral-500">Delivery address</dt>
                        <dd class="mt-1 font-bold text-neutral-950">{{ $order->delivery_address }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-neutral-500">Pickup date</dt>
                        <dd class="mt-1 font-bold text-neutral-950">{{ $order->pickup_date?->format('M j, Y') ?? 'Not set' }}</dd>
                        <dd class="mt-1 text-sm text-neutral-600">{{ $order->pickup_time_window ?? 'No time window set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-neutral-500">Delivery date</dt>
                        <dd class="mt-1 font-bold text-neutral-950">{{ $order->delivery_date?->format('M j, Y') ?? 'Not set' }}</dd>
                        <dd class="mt-1 text-sm text-neutral-600">{{ $order->delivery_time_window ?? 'No time window set' }}</dd>
                    </div>
                </dl>

                @if ($order->pickup_notes || $order->delivery_notes)
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        @if ($order->pickup_notes)
                            <div class="rounded-md bg-stone-50 p-4">
                                <p class="text-sm font-bold text-neutral-700">Pickup notes</p>
                                <p class="mt-1 text-sm leading-6 text-neutral-600">{{ $order->pickup_notes }}</p>
                            </div>
                        @endif
                        @if ($order->delivery_notes)
                            <div class="rounded-md bg-stone-50 p-4">
                                <p class="text-sm font-bold text-neutral-700">Delivery notes</p>
                                <p class="mt-1 text-sm leading-6 text-neutral-600">{{ $order->delivery_notes }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($order->notes)
                    <div class="mt-5 rounded-md bg-stone-50 p-4">
                        <p class="text-sm font-bold text-neutral-700">Notes</p>
                        <p class="mt-1 text-sm leading-6 text-neutral-600">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-black text-neutral-950">Customer review</h2>
                @if ($order->review)
                    <div class="mt-5 rounded-md bg-amber-50 p-4">
                        <x-rating-stars :rating="$order->review->rating" />
                        @if ($order->review->comment)
                            <p class="mt-3 text-sm leading-6 text-neutral-700">{{ $order->review->comment }}</p>
                        @endif
                        <p class="mt-3 text-xs font-semibold text-neutral-500">Reviewed {{ $order->review->created_at->format('M j, Y') }}</p>
                    </div>
                @elseif (auth()->user()?->role === 'customer' && $order->isReviewableBy(auth()->user()))
                    <p class="mt-3 text-sm leading-6 text-neutral-600">This completed paid order is ready for feedback.</p>
                    <a href="{{ route('orders.review.create', $order) }}" class="mt-4 inline-flex rounded-md bg-amber-500 px-4 py-2 text-sm font-bold text-white hover:bg-amber-600">Leave Review</a>
                @else
                    <p class="mt-3 text-sm leading-6 text-neutral-600">No review has been submitted for this order yet.</p>
                @endif
            </div>

            <div class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-black text-neutral-950">Order activity</h2>
                <div class="mt-5 space-y-4">
                    @forelse ($order->activities as $activity)
                        <div class="border-l-4 border-teal-600 pl-4">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <p class="font-bold text-neutral-950">{{ str($activity->action)->replace('_', ' ')->title() }}</p>
                                <p class="text-xs font-semibold text-neutral-500">{{ $activity->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                            <p class="mt-1 text-sm leading-6 text-neutral-600">{{ $activity->description }}</p>
                            @if ($activity->user)
                                <p class="mt-1 text-xs text-neutral-500">By {{ $activity->user->name }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-neutral-600">No activity has been logged for this order yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <aside class="h-fit rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-black text-neutral-950">Summary</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-neutral-600">Subtotal</dt>
                    <dd class="font-bold">NGN {{ number_format((float) $order->subtotal, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-neutral-600">Delivery fee</dt>
                    <dd class="font-bold">NGN {{ number_format((float) $order->delivery_fee, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-neutral-600">Platform fee</dt>
                    <dd class="font-bold">NGN {{ number_format((float) $order->platform_fee, 2) }}</dd>
                </div>
                <div class="flex justify-between border-t border-neutral-200 pt-3 text-base">
                    <dt class="font-black">Total</dt>
                    <dd class="font-black text-teal-800">NGN {{ number_format((float) $order->total, 2) }}</dd>
                </div>
            </dl>

            <div class="mt-5 border-t border-neutral-200 pt-5">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-bold text-neutral-700">Payment</p>
                    <x-payment-status-badge :status="$order->payment_status" />
                </div>

                @if ($order->payment?->reference)
                    <p class="mt-3 break-all text-xs text-neutral-500">Reference: {{ $order->payment->reference }}</p>
                @endif

                @if ($order->payment_status === 'paid')
                    <div class="mt-4 rounded-md bg-teal-50 p-4 text-sm font-semibold text-teal-900">
                        Paid{{ $order->paid_at ? ' on '.$order->paid_at->format('M j, Y') : '' }}.
                    </div>
                @elseif ($order->payment_status === 'pending')
                    <div class="mt-4 rounded-md bg-sky-50 p-4 text-sm font-semibold text-sky-900">
                        Payment is pending manual confirmation.
                    </div>
                @elseif (auth()->user()?->role === 'customer' && in_array($order->payment_status, ['unpaid', 'failed'], true))
                    <div class="mt-4 rounded-md bg-neutral-50 p-4 text-sm leading-6 text-neutral-700">
                        Online payment is disabled for this deployment. Confirm payment with the cleaner or admin offline.
                    </div>
                @endif
            </div>
        </aside>
    </section>
</x-layouts.app>
