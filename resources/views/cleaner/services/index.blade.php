<x-layouts.app title="Manage Services - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell flex flex-col gap-4 py-12 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Cleaner services</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Manage services and pricing</h1>
                <p class="mt-4 max-w-2xl text-neutral-600">Create, update, hide, or remove service options customers can choose when placing orders.</p>
            </div>
            @if ($cleaner)
                <a href="{{ route('cleaner.services.create') }}" class="inline-flex h-fit rounded-md bg-teal-700 px-5 py-3 text-sm font-bold text-white hover:bg-teal-800">Add Service</a>
            @endif
        </div>
    </section>

    <section class="container-shell py-10">
        @if (! $cleaner)
            <div class="rounded-lg border border-dashed border-neutral-300 bg-white p-8 text-neutral-600">
                Create your cleaner profile before adding services.
                <a href="{{ route('cleaner.profile.edit') }}" class="font-bold text-teal-800">Set up profile</a>.
            </div>
        @else
            <div class="rounded-lg border border-neutral-200 bg-white shadow-sm">
                <div class="border-b border-neutral-200 p-5">
                    <h2 class="text-xl font-black text-neutral-950">{{ $cleaner->business_name }}</h2>
                    <p class="mt-1 text-sm text-neutral-600">{{ $services->where('is_active', true)->count() }} active of {{ $services->count() }} services</p>
                </div>

                <div class="divide-y divide-neutral-200">
                    @forelse ($services as $service)
                        <div class="p-5">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-black text-neutral-950">{{ $service->name }}</p>
                                        <x-service-status-badge :active="$service->is_active" />
                                    </div>
                                    <p class="mt-2 max-w-2xl text-sm leading-6 text-neutral-600">{{ $service->description ?? 'No description yet.' }}</p>
                                    <p class="mt-2 text-sm font-bold text-teal-800">NGN {{ number_format((float) $service->price, 2) }} / {{ str($service->unit)->replace('_', ' ') }}</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('cleaner.services.edit', $service) }}" class="rounded-md border border-neutral-300 px-3 py-2 text-xs font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Edit</a>
                                    <form method="POST" action="{{ route('cleaner.services.destroy', $service) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md border border-rose-300 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-50">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-neutral-600">
                            No services yet. <a href="{{ route('cleaner.services.create') }}" class="font-bold text-teal-800">Add your first service</a>.
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </section>
</x-layouts.app>
