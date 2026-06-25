<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-md border border-transparent bg-teal-700 px-4 py-2 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-teal-800 focus:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-700 focus:ring-offset-2 active:bg-teal-900']) }}>
    {{ $slot }}
</button>
