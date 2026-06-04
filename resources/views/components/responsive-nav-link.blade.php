@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-amber text-start text-base font-medium text-ink bg-amber/10 focus:outline-none focus:text-ink focus:bg-amber/20 focus:border-amber transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-ink-50 hover:text-ink-70 hover:bg-ink-10 hover:border-ink-30 focus:outline-none focus:text-ink-70 focus:bg-ink-10 focus:border-ink-30 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
