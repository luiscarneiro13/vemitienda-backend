<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center min-h-[44px] px-5 py-2.5 bg-white border border-[#e3deec] rounded-[10px] font-semibold text-sm text-[#242034] shadow-sm hover:bg-[#faf9fc] hover:border-[#c5bed2] active:bg-[#eee8fb]/60 active:translate-y-px focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6135bc] focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none transition duration-150 ease-in-out']) }}>
    {{ $slot }}
</button>
