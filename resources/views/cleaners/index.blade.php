<x-layouts.app title="Browse Cleaners - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell py-12">
            <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Cleaner marketplace</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Browse approved cleaners</h1>
            <p class="mt-4 max-w-2xl text-neutral-600">Compare ratings, locations, turnaround times, and service menus before starting an order.</p>
        </div>
    </section>

    <section class="container-shell py-10">
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($cleaners as $cleaner)
                <x-cleaner-card :cleaner="$cleaner" />
            @empty
                <div class="rounded-lg border border-dashed border-neutral-300 bg-white p-8 text-neutral-600 md:col-span-2 xl:col-span-3">
                    No approved cleaners yet. Seed the database to load the sample marketplace.
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.app>
