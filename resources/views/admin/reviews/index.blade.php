<x-layouts.app title="Review Moderation - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell py-12">
            <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Admin moderation</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Customer reviews</h1>
            <p class="mt-4 max-w-2xl text-neutral-600">Review customer feedback and remove inappropriate submissions when needed.</p>
        </div>
    </section>

    <section class="container-shell py-10">
        <div class="rounded-lg border border-neutral-200 bg-white shadow-sm">
            <div class="border-b border-neutral-200 p-5">
                <h2 class="text-xl font-black text-neutral-950">All reviews</h2>
            </div>
            <div class="divide-y divide-neutral-200">
                @forelse ($reviews as $review)
                    <div class="p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <p class="font-black text-neutral-950">{{ $review->cleaner?->business_name }}</p>
                                    <x-rating-stars :rating="$review->rating" />
                                </div>
                                <p class="mt-2 text-sm text-neutral-600">{{ $review->customer?->name ?? 'LaundryLink customer' }} - Order #{{ str_pad((string) $review->order_id, 4, '0', STR_PAD_LEFT) }} - {{ $review->created_at->format('M j, Y') }}</p>
                                @if ($review->comment)
                                    <p class="mt-3 max-w-3xl text-sm leading-6 text-neutral-600">{{ $review->comment }}</p>
                                @else
                                    <p class="mt-3 text-sm text-neutral-500">No comment provided.</p>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md border border-rose-300 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-50">Delete Review</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-neutral-600">No reviews have been submitted yet.</div>
                @endforelse
            </div>
        </div>

        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    </section>
</x-layouts.app>
