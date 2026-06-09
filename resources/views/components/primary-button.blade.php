<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2.5 bg-amber border border-amber rounded-sm font-semibold text-sm text-ink font-heading hover:brightness-110 hover:shadow-[0_4px_16px_rgba(255,180,0,0.3)] focus:outline-none transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
