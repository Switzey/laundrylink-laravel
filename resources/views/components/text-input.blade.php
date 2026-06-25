@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-md border-neutral-300 shadow-sm focus:border-teal-700 focus:ring-teal-700']) }}>
