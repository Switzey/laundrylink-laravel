<x-layouts.app title="Start an Order - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell py-12">
            <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">New order</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Create a laundry order</h1>
            <p class="mt-4 max-w-2xl text-neutral-600">Choose an approved cleaner, select services, add pickup details, and send the order for cleaner review.</p>
        </div>
    </section>

    <form action="{{ route('orders.store') }}" method="POST" class="container-shell grid gap-8 py-10 lg:grid-cols-[1fr_360px]">
        @csrf

        <div class="space-y-8">
            <section class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                <label for="cleaner_id" class="text-sm font-bold text-neutral-950">Cleaner</label>
                <select id="cleaner_id" name="cleaner_id" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                    <option value="">Select a cleaner</option>
                    @foreach ($cleaners as $cleaner)
                        <option value="{{ $cleaner->id }}" @selected((int) old('cleaner_id', $selectedCleaner?->id) === $cleaner->id)>
                            {{ $cleaner->business_name }} - {{ $cleaner->city }}
                        </option>
                    @endforeach
                </select>
                @error('cleaner_id')
                    <p class="mt-2 text-sm font-semibold text-rose-700">{{ $message }}</p>
                @enderror

                @if ($selectedCleaner)
                    <div class="mt-4 rounded-md bg-teal-50 p-4 text-sm text-teal-900">
                        Selected cleaner: <strong>{{ $selectedCleaner->business_name }}</strong> in {{ $selectedCleaner->city }}.
                    </div>
                @else
                    <p class="mt-3 text-sm text-neutral-600">Pick quantities only for the cleaner you choose above.</p>
                @endif
            </section>

            <section>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Service quantities</p>
                        <h2 class="mt-2 text-2xl font-black text-neutral-950">Choose what needs cleaning</h2>
                    </div>
                </div>
                @error('services')
                    <p class="mt-3 text-sm font-semibold text-rose-700">{{ $message }}</p>
                @enderror

                <div class="mt-5 space-y-6">
                    @php
                        $serviceCleaners = $selectedCleaner ? collect([$selectedCleaner]) : $cleaners;
                    @endphp

                    @forelse ($serviceCleaners as $cleaner)
                        <div>
                            <h3 class="mb-3 text-lg font-black text-neutral-950">{{ $cleaner->business_name }}</h3>
                            <div class="grid gap-4">
                                @forelse ($cleaner->services as $service)
                                    <x-service-card :service="$service" :with-quantity="true" />
                                @empty
                                    <div class="rounded-lg border border-dashed border-neutral-300 bg-white p-6 text-sm text-neutral-600">
                                        This cleaner has no active services right now.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-neutral-300 bg-white p-8 text-neutral-600">
                            No available cleaner services are available yet.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-neutral-950">Pickup and delivery</h2>
                        <p class="mt-2 text-sm text-neutral-600">Use a saved address or enter a one-time location for this order.</p>
                    </div>
                    <a href="{{ route('customer.addresses.index') }}" class="text-sm font-bold text-teal-800 hover:text-teal-900">Manage addresses</a>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <div class="space-y-3 rounded-md bg-stone-50 p-4">
                        <label class="block">
                            <span class="text-sm font-bold text-neutral-700">Saved pickup address</span>
                            <select name="pickup_address_id" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                                <option value="">Use one-time pickup address</option>
                                @foreach ($addresses as $address)
                                    <option value="{{ $address->id }}" @selected((int) old('pickup_address_id', $defaultAddress?->id) === $address->id)>
                                        {{ $address->label ?: 'Saved address' }} - {{ $address->city }}{{ $address->is_default ? ' (default)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pickup_address_id') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                        </label>

                        <label class="block">
                            <span class="text-sm font-bold text-neutral-700">One-time pickup address</span>
                            <input name="pickup_address" type="text" value="{{ old('pickup_address', $defaultAddress ? '' : auth()->user()->address) }}" placeholder="Enter pickup address" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                            @error('pickup_address') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <div class="space-y-3 rounded-md bg-stone-50 p-4">
                        <label class="block">
                            <span class="text-sm font-bold text-neutral-700">Saved delivery address</span>
                            <select name="delivery_address_id" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                                <option value="">Use one-time delivery address</option>
                                @foreach ($addresses as $address)
                                    <option value="{{ $address->id }}" @selected((int) old('delivery_address_id', $defaultAddress?->id) === $address->id)>
                                        {{ $address->label ?: 'Saved address' }} - {{ $address->city }}{{ $address->is_default ? ' (default)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('delivery_address_id') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                        </label>

                        <label class="block">
                            <span class="text-sm font-bold text-neutral-700">One-time delivery address</span>
                            <input name="delivery_address" type="text" value="{{ old('delivery_address', $defaultAddress ? '' : auth()->user()->address) }}" placeholder="Enter delivery address" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                            @error('delivery_address') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <label class="block">
                        <span class="text-sm font-bold text-neutral-700">Pickup date</span>
                        <input name="pickup_date" type="date" value="{{ old('pickup_date') }}" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                        @error('pickup_date') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="text-sm font-bold text-neutral-700">Pickup time window</span>
                        <select name="pickup_time_window" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                            <option value="">Choose a pickup window</option>
                            @foreach ($timeWindows as $window)
                                <option value="{{ $window }}" @selected(old('pickup_time_window') === $window)>{{ $window }}</option>
                            @endforeach
                        </select>
                        @error('pickup_time_window') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="text-sm font-bold text-neutral-700">Delivery date</span>
                        <input name="delivery_date" type="date" value="{{ old('delivery_date') }}" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                        @error('delivery_date') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="text-sm font-bold text-neutral-700">Delivery time window</span>
                        <select name="delivery_time_window" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                            <option value="">Choose a delivery window if known</option>
                            @foreach ($timeWindows as $window)
                                <option value="{{ $window }}" @selected(old('delivery_time_window') === $window)>{{ $window }}</option>
                            @endforeach
                        </select>
                        @error('delivery_time_window') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="text-sm font-bold text-neutral-700">Pickup notes</span>
                        <textarea name="pickup_notes" rows="3" placeholder="Gate access, pickup contact, or pickup instructions" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">{{ old('pickup_notes') }}</textarea>
                        @error('pickup_notes') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="text-sm font-bold text-neutral-700">Delivery notes</span>
                        <textarea name="delivery_notes" rows="3" placeholder="Drop-off notes or delivery contact" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">{{ old('delivery_notes') }}</textarea>
                        @error('delivery_notes') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                    </label>
                </div>
                <label class="mt-4 block">
                    <span class="text-sm font-bold text-neutral-700">Notes</span>
                    <textarea name="notes" rows="4" placeholder="Add pickup instructions or fabric care notes" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">{{ old('notes') }}</textarea>
                    @error('notes') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>
            </section>
        </div>

        <aside class="h-fit rounded-lg border border-neutral-200 bg-white p-5 shadow-sm lg:sticky lg:top-28">
            <h2 class="text-xl font-black text-neutral-950">Order summary</h2>
            <div class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-neutral-600">Service subtotal</span>
                    <span class="font-bold">Calculated from quantities</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-neutral-600">Delivery fee</span>
                    <span class="font-bold">NGN {{ number_format($deliveryFee, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-neutral-600">Platform fee</span>
                    <span class="font-bold">NGN {{ number_format($platformFee, 2) }}</span>
                </div>
                <div class="border-t border-neutral-200 pt-3">
                    <p class="text-sm leading-6 text-neutral-600">Final total is calculated securely from the services table when you submit.</p>
                </div>
            </div>
            <button type="submit" class="mt-6 w-full rounded-md bg-teal-700 px-5 py-3 text-sm font-bold text-white hover:bg-teal-800">
                Create Order
            </button>
        </aside>
    </form>
</x-layouts.app>
