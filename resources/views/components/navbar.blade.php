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
            @guest
                <a href="{{ route('orders.create') }}" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">New Order</a>
            @endguest
            @auth
                @php
                    $unreadNotificationsCount = auth()->user()->appNotifications()->whereNull('read_at')->count();
                @endphp
                @if (auth()->user()->role === 'customer')
                    <a href="{{ route('orders.create') }}" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">New Order</a>
                    <a href="{{ route('customer.addresses.index') }}" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">Addresses</a>
                @elseif (auth()->user()->role === 'cleaner')
                    <a href="{{ route('vendor.schedule') }}" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">Vendor Schedule</a>
                    <a href="{{ route('vendor.reports') }}" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">Vendor Reports</a>
                @elseif (auth()->user()->role === 'admin')
                    <a href="{{ route('admin.logistics') }}" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">Logistics</a>
                    <a href="{{ route('admin.reports') }}" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">Reports</a>
                @endif
                <a href="{{ route('dashboard') }}" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">
                    {{ auth()->user()->role === 'cleaner' ? 'Vendor Dashboard' : (auth()->user()->role === 'customer' ? 'Client Dashboard' : 'Admin Dashboard') }}
                </a>
                <a href="{{ route('notifications.index') }}" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">
                    Notifications
                    @if ($unreadNotificationsCount > 0)
                        <span class="ml-1 rounded-full bg-rose-600 px-2 py-0.5 text-xs font-bold text-white">{{ $unreadNotificationsCount }}</span>
                    @endif
                </a>
                <a href="{{ route('profile.edit') }}" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">Log out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="rounded-full px-3 py-2 hover:bg-teal-50 hover:text-teal-800">Log in</a>
                <a href="{{ route('register') }}" class="rounded-full bg-teal-700 px-4 py-2 font-bold text-white hover:bg-teal-800">Register</a>
            @endauth
        </div>
    </nav>
</header>
