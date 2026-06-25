<x-layouts.app title="Edit Address - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell py-12">
            <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Address book</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Edit address</h1>
            <p class="mt-4 max-w-2xl text-neutral-600">Update saved pickup and delivery details.</p>
        </div>
    </section>

    <section class="container-shell max-w-3xl py-10">
        @include('customer.addresses.partials.form', [
            'address' => $address,
            'action' => route('customer.addresses.update', $address),
            'method' => 'PATCH',
            'submitLabel' => 'Update Address',
        ])
    </section>
</x-layouts.app>
