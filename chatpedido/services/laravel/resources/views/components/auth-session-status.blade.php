@props(['status'])

@if ($status)
    <div role="status" {{ $attributes->merge(['class' => 'rounded-[10px] bg-emerald-50 border border-emerald-200 px-4 py-3 font-medium text-sm text-emerald-800']) }}>
        {{ $status }}
    </div>
@endif
