<x-layouts.app title="Address Book - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell flex flex-col gap-4 py-12 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Customer addresses</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Address book</h1>
                <p class="mt-4 max-w-2xl text-neutral-600">Save pickup and delivery addresses so order scheduling stays quick and consistent.</p>
            </div>
            <a href="{{ route('customer.addresses.create') }}" class="rounded-md bg-teal-700 px-5 py-3 text-sm font-bold text-white hover:bg-teal-800">Add Address</a>
        </div>
    </section>

    <section class="container-shell py-10">
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($addresses as $address)
                <article class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-black text-neutral-950">{{ $address->label ?: 'Saved address' }}</h2>
                            <p class="mt-1 text-sm font-semibold text-neutral-600">{{ $address->city }}</p>
                        </div>
                        @if ($address->is_default)
                            <span class="rounded-full bg-teal-100 px-3 py-1 text-xs font-bold text-teal-800">Default</span>
                        @endif
                    </div>

                    <p class="mt-4 text-sm leading-6 text-neutral-700">{{ $address->address }}</p>
                    @if ($address->phone)
                        <p class="mt-2 text-sm text-neutral-600">{{ $address->phone }}</p>
                    @endif
                    @if ($address->delivery_notes)
                        <p class="mt-3 rounded-md bg-stone-50 p-3 text-sm leading-6 text-neutral-600">{{ $address->delivery_notes }}</p>
                    @endif

                    <div class="mt-5 flex flex-wrap gap-2 border-t border-neutral-200 pt-4">
                        <a href="{{ route('customer.addresses.edit', $address) }}" class="rounded-md border border-neutral-300 px-3 py-2 text-xs font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Edit</a>
                        <form method="POST" action="{{ route('customer.addresses.destroy', $address) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-md border border-rose-300 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-50">Delete</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-dashed border-neutral-300 bg-white p-8 text-neutral-600 md:col-span-2 xl:col-span-3">
                    No saved addresses yet. Add your home or office to make the next order smoother.
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.app>
