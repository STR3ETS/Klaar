<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-snow border border-ink-30 rounded-md font-semibold text-sm text-ink-70 tracking-wide shadow-sm hover:bg-ink-10 focus:outline-none focus:ring-2 focus:ring-amber focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
