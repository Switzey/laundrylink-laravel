<x-layouts.app title="Notifications - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell flex flex-col gap-4 py-12 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">In-app notifications</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Notifications</h1>
                <p class="mt-4 max-w-2xl text-neutral-600">Follow order updates, payments, approvals, and customer feedback in one place.</p>
            </div>
            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded-md bg-teal-700 px-4 py-3 text-sm font-bold text-white hover:bg-teal-800">Mark all as read</button>
                </form>
            @endif
        </div>
    </section>

    <section class="container-shell py-10">
        <div class="rounded-lg border border-neutral-200 bg-white shadow-sm">
            <div class="border-b border-neutral-200 p-5">
                <h2 class="text-xl font-black text-neutral-950">{{ $unreadCount }} unread</h2>
            </div>
            <div class="divide-y divide-neutral-200">
                @forelse ($notifications as $notification)
                    <div class="p-5 {{ $notification->read_at ? '' : 'bg-teal-50/40' }}">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-notification-type-badge :type="$notification->type" />
                                    @unless ($notification->read_at)
                                        <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-800">Unread</span>
                                    @endunless
                                </div>
                                <h3 class="mt-3 text-lg font-black text-neutral-950">{{ $notification->title }}</h3>
                                <p class="mt-2 text-sm leading-6 text-neutral-600">{{ $notification->message }}</p>
                                <p class="mt-3 text-xs font-semibold text-neutral-500">{{ $notification->created_at->format('M j, Y g:i A') }}</p>
                            </div>

                            @unless ($notification->read_at)
                                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-md border border-neutral-300 px-3 py-2 text-xs font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Mark as read</button>
                                </form>
                            @endunless
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-neutral-600">No notifications yet. Order updates and payment events will appear here.</div>
                @endforelse
            </div>
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    </section>
</x-layouts.app>
