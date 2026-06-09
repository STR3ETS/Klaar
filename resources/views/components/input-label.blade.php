@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-[10px] font-heading font-semibold uppercase tracking-wider text-ink-50 mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
