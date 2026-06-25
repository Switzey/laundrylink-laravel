<x-layouts.app title="{{ config('app.name', 'LaundryLink') }}">
    @isset($header)
        <section class="border-b border-neutral-200 bg-white">
            <div class="container-shell py-8">
                {{ $header }}
            </div>
        </section>
    @endisset

    <section class="container-shell py-10">
        {{ $slot }}
    </section>
</x-layouts.app>
