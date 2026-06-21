<x-layouts.app title="Start an Order - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell py-12">
            <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">New order</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Create a laundry order</h1>
            <p class="mt-4 max-w-2xl text-neutral-600">Stage 1 keeps this as a clickable UI scaffold. The form is ready for service quantities, addresses, dates, and notes, but it does not save yet.</p>
        </div>
    </section>

    <form action="#" method="get" class="container-shell grid gap-8 py-10 lg:grid-cols-[1fr_360px]">
        <div class="space-y-8">
            <section class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                <label for="cleaner_id" class="text-sm font-bold text-neutral-950">Cleaner</label>
                <select id="cleaner_id" name="cleaner_id" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                    <option value="">Select a cleaner</option>
                    @foreach ($cleaners as $cleaner)
                        <option value="{{ $cleaner->id }}" @selected($selectedCleaner?->is($cleaner))>{{ $cleaner->business_name }} - {{ $cleaner->city }}</option>
                    @endforeach
                </select>

                @if ($selectedCleaner)
                    <div class="mt-4 rounded-md bg-teal-50 p-4 text-sm text-teal-900">
                        Selected cleaner: <strong>{{ $selectedCleaner->business_name }}</strong> in {{ $selectedCleaner->city }}.
                    </div>
                @endif
            </section>

            <section>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Service quantities</p>
                        <h2 class="mt-2 text-2xl font-black text-neutral-950">Choose what needs cleaning</h2>
                    </div>
                </div>

                <div class="mt-5 space-y-6">
                    @php
                        $serviceCleaners = $selectedCleaner ? collect([$selectedCleaner]) : $cleaners;
                    @endphp

                    @forelse ($serviceCleaners as $cleaner)
                        <div>
                            <h3 class="mb-3 text-lg font-black text-neutral-950">{{ $cleaner->business_name }}</h3>
                            <div class="grid gap-4">
                                @foreach ($cleaner->services as $service)
                                    <x-service-card :service="$service" :with-quantity="true" />
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-neutral-300 bg-white p-8 text-neutral-600">
                            Seed the database to choose from sample cleaner services.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                <h2 class="text-2xl font-black text-neutral-950">Pickup and delivery</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-bold text-neutral-700">Pickup address</span>
                        <input name="pickup_address" type="text" placeholder="Enter pickup address" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                    </label>
                    <label class="block">
                        <span class="text-sm font-bold text-neutral-700">Delivery address</span>
                        <input name="delivery_address" type="text" placeholder="Enter delivery address" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                    </label>
                    <label class="block">
                        <span class="text-sm font-bold text-neutral-700">Pickup date</span>
                        <input name="pickup_date" type="date" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                    </label>
                    <label class="block">
                        <span class="text-sm font-bold text-neutral-700">Delivery date</span>
                        <input name="delivery_date" type="date" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                    </label>
                </div>
                <label class="mt-4 block">
                    <span class="text-sm font-bold text-neutral-700">Notes</span>
                    <textarea name="notes" rows="4" placeholder="Add pickup instructions or fabric care notes" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100"></textarea>
                </label>
            </section>
        </div>

        <aside class="h-fit rounded-lg border border-neutral-200 bg-white p-5 shadow-sm lg:sticky lg:top-28">
            <h2 class="text-xl font-black text-neutral-950">Order summary</h2>
            <div class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-neutral-600">Service subtotal</span>
                    <span class="font-bold">NGN 0.00</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-neutral-600">Delivery fee</span>
                    <span class="font-bold">Calculated later</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-neutral-600">Platform fee</span>
                    <span class="font-bold">Calculated later</span>
                </div>
                <div class="border-t border-neutral-200 pt-3">
                    <div class="flex justify-between text-base">
                        <span class="font-black">Estimated total</span>
                        <span class="font-black text-teal-800">NGN 0.00</span>
                    </div>
                </div>
            </div>
            <button type="button" class="mt-6 w-full rounded-md bg-teal-700 px-5 py-3 text-sm font-bold text-white hover:bg-teal-800">
                Preview Order
            </button>
            <p class="mt-3 text-xs leading-5 text-neutral-500">Saving orders comes in a later stage. This button is intentionally non-submitting.</p>
        </aside>
    </form>
</x-layouts.app>
