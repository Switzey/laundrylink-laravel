<x-layouts.app title="{{ config('app.name', 'LaundryLink') }} Auth">
    <section class="container-shell flex min-h-[calc(100vh-180px)] items-center justify-center py-12">
        <div class="w-full max-w-xl rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
            <div class="mb-6">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-700 text-sm font-black text-white">LL</span>
                    <span>
                        <span class="block text-lg font-bold tracking-tight">LaundryLink</span>
                        <span class="block text-xs font-medium uppercase tracking-[0.18em] text-teal-700">Account access</span>
                    </span>
                </a>
            </div>

            {{ $slot }}
        </div>
    </section>
</x-layouts.app>
