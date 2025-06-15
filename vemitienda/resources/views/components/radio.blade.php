<div class=" mb-3 col-md-{{ @$columns }}" class="mt-4">
    <div class="form-group p-2">
        @if (@$label)
            <label class="control-label">{{ @$label }} @if (@$required) <span class="text-danger">*</span> @endif
            </label>
        @endif
        <div class="row">
            @foreach (@$datos as $opcion)
                <div class="form-check col-md-{{ @$columnsSeparate }}">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" value="{{ @$opcion['id'] }}"
                            name="{{ @$name }}"
                            @if (isset($selected)) @if ($selected === $opcion['id'])  checked @endif
                            @endif> <strong>{{ $opcion['label'] }} </strong>
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>
