@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center w-full min-h-[44px] ps-3 pe-4 py-2.5 border-l-4 border-[#6135bc] text-start text-base font-semibold text-[#6135bc] bg-[#eee8fb]/70 focus:outline-none focus-visible:bg-[#eee8fb] focus-visible:border-[#4c269c] transition duration-150 ease-in-out'
            : 'flex items-center w-full min-h-[44px] ps-3 pe-4 py-2.5 border-l-4 border-transparent text-start text-base font-medium text-[#665f74] hover:text-[#242034] hover:bg-[#faf9fc] hover:border-[#e3deec] focus:outline-none focus-visible:text-[#242034] focus-visible:bg-[#faf9fc] focus-visible:border-[#c5bed2] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
