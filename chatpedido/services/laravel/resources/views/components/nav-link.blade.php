@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-2 pt-1 border-b-2 border-[#6135bc] text-sm font-semibold leading-5 text-[#242034] focus:outline-none focus-visible:border-[#4c269c] transition duration-150 ease-in-out'
            : 'inline-flex items-center px-2 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-[#665f74] hover:text-[#242034] hover:border-[#e3deec] focus:outline-none focus-visible:text-[#242034] focus-visible:border-[#c5bed2] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
