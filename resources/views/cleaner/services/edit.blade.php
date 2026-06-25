<x-layouts.app title="Edit Service - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell flex flex-col gap-4 py-12 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Cleaner services</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Edit {{ $service->name }}</h1>
                <p class="mt-4 max-w-2xl text-neutral-600">Update customer-facing service details, price, unit, and active status.</p>
            </div>
            <x-service-status-badge :active="$service->is_active" />
        </div>
    </section>

    <section class="container-shell py-10">
        <form method="POST" action="{{ route('cleaner.services.update', $service) }}" class="grid gap-4 rounded-lg border border-neutral-200 bg-white p-5 shadow-sm md:grid-cols-2">
            @csrf
            @method('PATCH')
            @include('cleaner.services.partials.form', [
                'service' => $service,
                'submitLabel' => 'Update Service',
            ])
        </form>
    </section>
</x-layouts.app>
