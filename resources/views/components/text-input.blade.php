@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-ink-30 focus:border-amber focus:ring-amber rounded-md shadow-sm']) }}>
