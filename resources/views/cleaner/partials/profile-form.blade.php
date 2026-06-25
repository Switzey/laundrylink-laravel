<label class="block md:col-span-2">
    <span class="text-sm font-bold text-neutral-700">Business name</span>
    <input name="business_name" type="text" value="{{ old('business_name', $cleaner?->business_name) }}" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
    @error('business_name') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
</label>

<label class="block md:col-span-2">
    <span class="text-sm font-bold text-neutral-700">Description</span>
    <textarea name="description" rows="3" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">{{ old('description', $cleaner?->description) }}</textarea>
    @error('description') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
</label>

<label class="block md:col-span-2">
    <span class="text-sm font-bold text-neutral-700">Address</span>
    <input name="address" type="text" value="{{ old('address', $cleaner?->address) }}" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
    @error('address') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
</label>

<label class="block">
    <span class="text-sm font-bold text-neutral-700">City</span>
    <input name="city" type="text" value="{{ old('city', $cleaner?->city) }}" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
    @error('city') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
</label>

<label class="block">
    <span class="text-sm font-bold text-neutral-700">Phone</span>
    <input name="phone" type="text" value="{{ old('phone', $cleaner?->phone) }}" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
    @error('phone') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
</label>

<label class="block md:col-span-2">
    <span class="text-sm font-bold text-neutral-700">Turnaround time</span>
    <input name="turnaround_time" type="text" value="{{ old('turnaround_time', $cleaner?->turnaround_time) }}" placeholder="24-48 hours" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
    @error('turnaround_time') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
</label>

<label class="block md:col-span-2">
    <span class="text-sm font-bold text-neutral-700">Opening hours</span>
    <input name="opening_hours" type="text" value="{{ old('opening_hours', $cleaner?->opening_hours) }}" placeholder="Mon-Sat, 8am-6pm" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
    @error('opening_hours') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
</label>

<label class="flex items-start gap-3 rounded-md border border-neutral-200 bg-neutral-50 p-4 md:col-span-2">
    <input type="hidden" name="is_available" value="0">
    <input name="is_available" type="checkbox" value="1" @checked((bool) old('is_available', $cleaner?->is_available ?? true)) class="mt-1 rounded border-neutral-300 text-teal-700 focus:ring-teal-700">
    <span>
        <span class="block text-sm font-bold text-neutral-800">Available for new customer orders</span>
        <span class="mt-1 block text-sm leading-6 text-neutral-600">Turn this off when your business is paused, fully booked, or not accepting new work.</span>
    </span>
    @error('is_available') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
</label>

<div class="md:col-span-2">
    <button type="submit" class="w-full rounded-md bg-teal-700 px-4 py-3 text-sm font-bold text-white hover:bg-teal-800">
        {{ $submitLabel ?? 'Save Profile' }}
    </button>
</div>
