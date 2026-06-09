<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-ink-90 border border-ink-70/30 rounded-sm font-semibold text-sm text-paper/70 font-heading tracking-wide hover:bg-ink-70/20 hover:text-paper focus:outline-none disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
