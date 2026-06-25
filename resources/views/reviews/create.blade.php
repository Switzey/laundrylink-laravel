<x-layouts.app title="Review Order #{{ $order->id }} - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell py-12">
            <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Customer feedback</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Review {{ $order->cleaner?->business_name }}</h1>
            <p class="mt-4 max-w-2xl text-neutral-600">Share how your completed order went. Your review helps other customers choose with confidence.</p>
        </div>
    </section>

    <section class="container-shell grid gap-8 py-10 lg:grid-cols-[1fr_340px]">
        <form method="POST" action="{{ route('orders.review.store', $order) }}" class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
            @csrf

            <label class="block">
                <span class="text-sm font-bold text-neutral-700">Rating</span>
                <select name="rating" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                    <option value="">Choose a rating</option>
                    @for ($rating = 5; $rating >= 1; $rating--)
                        <option value="{{ $rating }}" @selected((int) old('rating') === $rating)>{{ $rating }} {{ $rating === 1 ? 'star' : 'stars' }}</option>
                    @endfor
                </select>
                @error('rating') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                @error('review') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
            </label>

            <label class="mt-5 block">
                <span class="text-sm font-bold text-neutral-700">Comment</span>
                <textarea name="comment" rows="6" maxlength="1000" placeholder="What went well? Anything future customers should know?" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">{{ old('comment') }}</textarea>
                @error('comment') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
            </label>

            <button type="submit" class="mt-6 w-full rounded-md bg-amber-500 px-4 py-3 text-sm font-bold text-white hover:bg-amber-600">
                Submit Review
            </button>
        </form>

        <aside class="h-fit rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-black text-neutral-950">Order summary</h2>
            <dl class="mt-5 space-y-4 text-sm">
                <div>
                    <dt class="text-neutral-500">Order</dt>
                    <dd class="font-bold text-neutral-950">#{{ str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) }}</dd>
                </div>
                <div>
                    <dt class="text-neutral-500">Cleaner</dt>
                    <dd class="font-bold text-neutral-950">{{ $order->cleaner?->business_name }}</dd>
                </div>
                <div>
                    <dt class="text-neutral-500">Total</dt>
                    <dd class="font-bold text-neutral-950">NGN {{ number_format((float) $order->total, 2) }}</dd>
                </div>
            </dl>
            <a href="{{ route('orders.show', $order) }}" class="mt-5 inline-flex w-full justify-center rounded-md border border-neutral-300 px-4 py-2 text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Back to Order</a>
        </aside>
    </section>
</x-layouts.app>
