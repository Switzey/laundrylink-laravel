<form method="POST" action="{{ $action }}" class="space-y-5 rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
    @csrf
    @if (! in_array(strtoupper($method), ['GET', 'POST'], true))
        @method($method)
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-bold text-neutral-700">Label</span>
            <input name="label" type="text" value="{{ old('label', $address->label) }}" placeholder="Home, Office, Shop" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
            @error('label') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
        </label>

        <label class="block">
            <span class="text-sm font-bold text-neutral-700">City</span>
            <input name="city" type="text" value="{{ old('city', $address->city) }}" placeholder="Lagos" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
            @error('city') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
        </label>
    </div>

    <label class="block">
        <span class="text-sm font-bold text-neutral-700">Address</span>
        <input name="address" type="text" value="{{ old('address', $address->address) }}" placeholder="Street, estate, or landmark" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
        @error('address') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm font-bold text-neutral-700">Phone</span>
        <input name="phone" type="text" value="{{ old('phone', $address->phone) }}" placeholder="Optional contact number" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
        @error('phone') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm font-bold text-neutral-700">Delivery notes</span>
        <textarea name="delivery_notes" rows="4" placeholder="Gate code, landmark, call instructions, or preferred drop-off notes" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">{{ old('delivery_notes', $address->delivery_notes) }}</textarea>
        @error('delivery_notes') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
    </label>

    <label class="flex items-start gap-3 rounded-md bg-stone-50 p-4 text-sm">
        <input name="is_default" type="checkbox" value="1" @checked(old('is_default', $address->is_default)) class="mt-1 rounded border-neutral-300 text-teal-700 focus:ring-teal-600">
        <span>
            <span class="block font-bold text-neutral-900">Use as default address</span>
            <span class="mt-1 block text-neutral-600">New orders can preselect this address for pickup and delivery.</span>
        </span>
    </label>

    <div class="flex flex-wrap gap-3 border-t border-neutral-200 pt-5">
        <button type="submit" class="rounded-md bg-teal-700 px-5 py-3 text-sm font-bold text-white hover:bg-teal-800">{{ $submitLabel }}</button>
        <a href="{{ route('customer.addresses.index') }}" class="rounded-md border border-neutral-300 px-5 py-3 text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Cancel</a>
    </div>
</form>
