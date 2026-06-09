@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'klaar-dark-input']) }}>
