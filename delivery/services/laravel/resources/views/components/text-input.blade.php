@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'min-h-[44px] px-3.5 py-2.5 bg-white border-[#c5bed2] text-[#242034] text-sm placeholder:text-[#665f74]/70 focus:border-[#6135bc] focus:ring-2 focus:ring-[#6135bc]/20 rounded-[10px] shadow-sm transition duration-150 ease-in-out disabled:bg-[#faf9fc] disabled:text-[#665f74] disabled:cursor-not-allowed']) }}>
