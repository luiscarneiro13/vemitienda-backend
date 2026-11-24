@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-[#242034] leading-snug']) }}>
    {{ $value ?? $slot }}
</label>
