<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2.5 bg-red-600 border border-red-600 rounded-sm font-semibold text-sm text-white font-heading hover:bg-red-500 focus:outline-none transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
