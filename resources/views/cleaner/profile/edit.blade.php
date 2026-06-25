<x-layouts.app title="Cleaner Profile - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell flex flex-col gap-4 py-12 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Cleaner profile</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">
                    {{ $cleaner ? 'Edit business profile' : 'Set up your business profile' }}
                </h1>
                <p class="mt-4 max-w-2xl text-neutral-600">Keep your cleaner information, hours, availability, and customer-facing details up to date.</p>
            </div>

            @if ($cleaner)
                <div class="flex flex-wrap gap-2">
                    <x-availability-status-badge :available="$cleaner->is_available" />
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $cleaner->is_approved ? 'bg-teal-100 text-teal-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $cleaner->is_approved ? 'Approved' : 'Pending approval' }}
                    </span>
                </div>
            @endif
        </div>
    </section>

    <section class="container-shell grid gap-8 py-10 lg:grid-cols-[1fr_340px]">
        <form method="POST" action="{{ route('cleaner.profile.update') }}" class="grid gap-4 rounded-lg border border-neutral-200 bg-white p-5 shadow-sm md:grid-cols-2">
            @csrf
            @method('PATCH')
            @include('cleaner.partials.profile-form', [
                'cleaner' => $cleaner,
                'submitLabel' => $cleaner ? 'Update Profile' : 'Create Profile',
            ])
        </form>

        <aside class="h-fit rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-black text-neutral-950">Profile status</h2>
            @if ($cleaner)
                <dl class="mt-5 space-y-4 text-sm">
                    <div>
                        <dt class="text-neutral-500">Approval</dt>
                        <dd class="mt-1 font-bold text-neutral-950">{{ $cleaner->is_approved ? 'Approved' : 'Pending approval' }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Availability</dt>
                        <dd class="mt-1"><x-availability-status-badge :available="$cleaner->is_available" /></dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Public visibility</dt>
                        <dd class="mt-1 font-bold text-neutral-950">{{ $cleaner->is_approved && $cleaner->is_available ? 'Visible to customers' : 'Hidden from marketplace' }}</dd>
                    </div>
                </dl>

                @unless ($cleaner->is_approved)
                    <div class="mt-5 rounded-md bg-amber-50 p-4 text-sm font-semibold leading-6 text-amber-900">
                        Customers may not see your business until an admin approves your profile.
                    </div>
                @endunless
            @else
                <p class="mt-3 text-sm leading-6 text-neutral-600">Create your profile first. Admin approval is required before customers can find your business.</p>
            @endif

            <div class="mt-5 grid gap-2 border-t border-neutral-200 pt-5">
                <a href="{{ route('cleaner.dashboard') }}" class="rounded-md border border-neutral-300 px-4 py-2 text-center text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Back to Dashboard</a>
                <a href="{{ route('cleaner.services.index') }}" class="rounded-md bg-teal-700 px-4 py-2 text-center text-sm font-bold text-white hover:bg-teal-800">Manage Services</a>
            </div>
        </aside>
    </section>
</x-layouts.app>
