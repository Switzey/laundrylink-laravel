<header class="sticky top-0 z-30 border-b border-neutral-200 bg-white/95 backdrop-blur">
    <nav class="container-shell flex flex-col gap-4 py-4 md:flex-row md:items-center md:justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-700 text-sm font-black text-white">LL</span>
            <span>
                <span class="block text-lg font-bold tracking-tight">LaundryLink</span>
                <span class="block text-xs font-medium uppercase tracking-[0.18em] text-teal-700">Cleaners on demand</span>
            </span>
        </a>

        <div class="flex flex-wrap items-center gap-2 text-sm font-medium text-neutral-700">
            <a href="{{ route('cleaners.index') }}" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">Cleaners</a>
            <a href="{{ route('orders.create') }}" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">New Order</a>
            <a href="{{ route('customer.dashboard') }}" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">Customer</a>
            <a href="{{ route('cleaner.dashboard') }}" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">Cleaner</a>
            <a href="{{ route('admin.dashboard') }}" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">Admin</a>
        </div>
    </nav>
</header>
