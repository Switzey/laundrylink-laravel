<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? config('app.name', 'LaundryLink') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen">
        <x-navbar />

        @if (session('success') || session('error') || session('status'))
            <div class="container-shell pt-6">
                @if (session('success'))
                    <div class="rounded-lg border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-semibold text-teal-900">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-900">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-900">
                        {{ session('status') }}
                    </div>
                @endif
            </div>
        @endif

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
