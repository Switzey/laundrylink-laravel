<x-layouts.app title="{{ $cleaner->business_name }} - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell grid gap-8 py-12 lg:grid-cols-[1fr_360px]">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">{{ $cleaner->city }}</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">{{ $cleaner->business_name }}</h1>
                <p class="mt-4 max-w-3xl text-lg leading-8 text-neutral-600">{{ $cleaner->description }}</p>
            </div>
            <aside class="rounded-lg border border-neutral-200 bg-stone-50 p-5">
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-neutral-500">Rating</dt>
                        <dd class="mt-1"><x-rating-stars :rating="$cleaner->rating" :count="$cleaner->reviews_count" /></dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Turnaround</dt>
                        <dd class="font-bold text-neutral-950">{{ $cleaner->turnaround_time ?? 'Flexible' }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Availability</dt>
                        <dd class="mt-1"><x-availability-status-badge :available="$cleaner->is_available" /></dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Opening hours</dt>
                        <dd class="font-bold text-neutral-950">{{ $cleaner->opening_hours ?? 'Not listed' }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Address</dt>
                        <dd class="font-bold text-neutral-950">{{ $cleaner->address }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Phone</dt>
                        <dd class="font-bold text-neutral-950">{{ $cleaner->phone }}</dd>
                    </div>
                </dl>
                @if ($cleaner->is_available && $cleaner->services->isNotEmpty())
                    <a href="{{ route('orders.create', ['cleaner' => $cleaner->id]) }}" class="mt-6 inline-flex w-full items-center justify-center rounded-md bg-teal-700 px-5 py-3 text-sm font-bold text-white hover:bg-teal-800">
                        Start Order
                    </a>
                @elseif (! $cleaner->is_available)
                    <div class="mt-6 rounded-md bg-rose-50 p-4 text-sm font-semibold text-rose-900">
                        This cleaner is unavailable for new pickup scheduling right now.
                    </div>
                @else
                    <div class="mt-6 rounded-md bg-amber-50 p-4 text-sm font-semibold text-amber-900">
                        This cleaner is not accepting service orders yet.
                    </div>
                @endif
            </aside>
        </div>
    </section>

    <section class="container-shell py-10">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Services</p>
                <h2 class="mt-2 text-3xl font-black text-neutral-950">Prices and care options</h2>
            </div>
        </div>

        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            @forelse ($cleaner->services as $service)
                <x-service-card :service="$service" />
            @empty
                <div class="rounded-lg border border-dashed border-neutral-300 bg-white p-8 text-neutral-600 lg:col-span-2">
                    This cleaner has no active services right now. Check back soon or browse another cleaner.
                </div>
            @endforelse
        </div>
    </section>

    <section class="container-shell pb-12">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Customer feedback</p>
                <h2 class="mt-2 text-3xl font-black text-neutral-950">Recent reviews</h2>
            </div>
            <x-rating-stars :rating="$cleaner->rating" :count="$cleaner->reviews_count" />
        </div>

        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            @forelse ($recentReviews as $review)
                <article class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="font-bold text-neutral-950">{{ $review->customer?->name ?? 'LaundryLink customer' }}</p>
                            <p class="mt-1 text-xs text-neutral-500">{{ $review->created_at->format('M j, Y') }}</p>
                        </div>
                        <x-rating-stars :rating="$review->rating" />
                    </div>
                    @if ($review->comment)
                        <p class="mt-4 text-sm leading-6 text-neutral-600">{{ $review->comment }}</p>
                    @endif
                </article>
            @empty
                <div class="rounded-lg border border-dashed border-neutral-300 bg-white p-8 text-neutral-600 lg:col-span-2">
                    No reviews yet. Completed paid orders can be reviewed by customers.
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.app>
