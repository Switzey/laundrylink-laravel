<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'LaundryLink') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen">
        <x-navbar />

        <main>
            {{ $slot }}
        </main>

        <footer class="border-t border-neutral-200 bg-white">
            <div class="container-shell flex flex-col gap-3 py-8 text-sm text-neutral-600 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} LaundryLink. Clean clothes, calmer weeks.</p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('cleaners.index') }}" class="hover:text-teal-700">Browse cleaners</a>
                    <a href="{{ route('orders.create') }}" class="hover:text-teal-700">Start an order</a>
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-teal-700">Admin</a>
                </div>
            </div>
        </footer>
    </body>
</html>
