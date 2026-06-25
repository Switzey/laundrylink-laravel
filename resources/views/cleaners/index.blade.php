<x-layouts.app title="Browse Cleaners - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell py-12">
            <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Cleaner marketplace</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Browse approved cleaners</h1>
            <p class="mt-4 max-w-2xl text-neutral-600">Compare ratings, locations, turnaround times, and service menus before starting an order.</p>
        </div>
    </section>

    <section class="container-shell py-10">
        <form method="GET" action="{{ route('cleaners.index') }}" class="mb-8 grid gap-3 rounded-lg border border-neutral-200 bg-white p-4 shadow-sm lg:grid-cols-[1fr_180px_160px_200px_auto]">
            <label class="block">
                <span class="text-sm font-bold text-neutral-700">Search</span>
                <input name="search" type="search" value="{{ $filters['search'] }}" placeholder="Business, city, or service" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
            </label>
            <label class="block">
                <span class="text-sm font-bold text-neutral-700">City</span>
                <select name="city" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                    <option value="">All cities</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city }}" @selected($filters['city'] === $city)>{{ $city }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="text-sm font-bold text-neutral-700">Min rating</span>
                <select name="min_rating" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                    <option value="">Any rating</option>
                    @foreach ([4, 3, 2, 1] as $rating)
                        <option value="{{ $rating }}" @selected((string) $filters['min_rating'] === (string) $rating)>{{ $rating }}+ stars</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="text-sm font-bold text-neutral-700">Sort</span>
                <select name="sort" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                    <option value="highest_rated" @selected($filters['sort'] === 'highest_rated')>Highest rated</option>
                    <option value="newest" @selected($filters['sort'] === 'newest')>Newest</option>
                    <option value="fastest_turnaround" @selected($filters['sort'] === 'fastest_turnaround')>Fastest turnaround</option>
                </select>
            </label>
            <div class="flex items-end gap-2">
                <button type="submit" class="rounded-md bg-teal-700 px-5 py-3 text-sm font-bold text-white hover:bg-teal-800">Filter</button>
                <a href="{{ route('cleaners.index') }}" class="rounded-md border border-neutral-300 px-5 py-3 text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Clear</a>
            </div>
        </form>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($cleaners as $cleaner)
                <x-cleaner-card :cleaner="$cleaner" />
            @empty
                <div class="rounded-lg border border-dashed border-neutral-300 bg-white p-8 text-neutral-600 md:col-span-2 xl:col-span-3">
                    No approved and available cleaners match those filters. Try a broader city, service, or rating.
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.app>
