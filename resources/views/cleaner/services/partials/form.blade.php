<label class="block md:col-span-2">
    <span class="text-sm font-bold text-neutral-700">Service name</span>
    <input name="name" type="text" value="{{ old('name', $service?->name) }}" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
    @error('name') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
</label>

<label class="block md:col-span-2">
    <span class="text-sm font-bold text-neutral-700">Description</span>
    <textarea name="description" rows="4" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">{{ old('description', $service?->description) }}</textarea>
    @error('description') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
</label>

<label class="block">
    <span class="text-sm font-bold text-neutral-700">Price</span>
    <input name="price" type="number" value="{{ old('price', $service?->price) }}" min="1" step="0.01" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
    @error('price') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
</label>

<label class="block">
    <span class="text-sm font-bold text-neutral-700">Unit</span>
    <select name="unit" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
        @foreach (['per_item' => 'Per item', 'per_kg' => 'Per kg', 'flat' => 'Flat'] as $value => $label)
            <option value="{{ $value }}" @selected(old('unit', $service?->unit ?? 'per_item') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('unit') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
</label>

<label class="flex items-start gap-3 rounded-md border border-neutral-200 bg-neutral-50 p-4 md:col-span-2">
    <input type="hidden" name="is_active" value="0">
    <input name="is_active" type="checkbox" value="1" @checked((bool) old('is_active', $service?->is_active ?? true)) class="mt-1 rounded border-neutral-300 text-teal-700 focus:ring-teal-700">
    <span>
        <span class="block text-sm font-bold text-neutral-800">Active and visible to customers</span>
        <span class="mt-1 block text-sm leading-6 text-neutral-600">Inactive services stay saved but are hidden from public cleaner pages and order creation.</span>
    </span>
    @error('is_active') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
</label>

<div class="md:col-span-2">
    <button type="submit" class="w-full rounded-md bg-teal-700 px-4 py-3 text-sm font-bold text-white hover:bg-teal-800">
        {{ $submitLabel ?? 'Save Service' }}
    </button>
</div>
