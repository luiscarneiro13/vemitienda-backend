<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center min-h-[44px] px-5 py-2.5 bg-red-600 border border-transparent rounded-[10px] font-semibold text-sm text-white shadow-sm hover:bg-red-700 active:bg-red-800 active:translate-y-px focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none transition duration-150 ease-in-out']) }}>
    {{ $slot }}
</button>
