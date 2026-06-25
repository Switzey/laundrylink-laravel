<x-layouts.app title="Add Service - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell py-12">
            <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Cleaner services</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Add a service</h1>
            <p class="mt-4 max-w-2xl text-neutral-600">Add a new customer-facing service with clear pricing and a unit.</p>
        </div>
    </section>

    <section class="container-shell py-10">
        <form method="POST" action="{{ route('cleaner.services.store') }}" class="grid gap-4 rounded-lg border border-neutral-200 bg-white p-5 shadow-sm md:grid-cols-2">
            @csrf
            @include('cleaner.services.partials.form', [
                'service' => $service,
                'submitLabel' => 'Create Service',
            ])
        </form>
    </section>
</x-layouts.app>
