<div class="col-md-{{@$columns}}">
    <div class="form-group">
        <label class="control-label">{{@$label}}
            @if(@$required)
            <span class="text-danger">*</span>
            @endif
        </label>

        <textarea id="{{@$id?$id:$name}}" name="{{@$name}}" id="" cols="30" rows="4" placeholder="{{@$placeholder}}"
            class="form-control {{@$class?$class:''}} @error(@$name) is-invalid
            @enderror" @if(@$disabled) disabled="true" @endif>{{@$value}}</textarea>

        @if (Session::has('errors') && @Session::get('errors')->first($name))
        <span class="invalid-feedback" role="alert">
            <strong>{{ Session::get('errors')->first($name) }}</strong>
        </span>
        @endif

        @if(@$small)
        <span class="text-muted"><small>{{@$small}}</small></span>
        @endif

    </div>
</div>