<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center min-h-[44px] px-5 py-2.5 bg-[#6135bc] border border-transparent rounded-[10px] font-semibold text-sm text-white shadow-sm hover:bg-[#4c269c] focus-visible:bg-[#4c269c] active:bg-[#3e207e] active:translate-y-px focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6135bc] focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none transition duration-150 ease-in-out']) }}>
    {{ $slot }}
</button>
