<div class="form-group col-md-{{@$columns?$columns:'12'}}">

    <label class="control-label">{{@$label}} @if(@$required) <span class="text-danger">*</span> @endif</label>

    <div class="input-group date" id="{{ @$id?$id:$name }}" data-target-input="nearest">
        <input id="{{ @$id?$id:$name }}" name="{{ @$name }}" type="text" class="form-control datetimepicker-input @error(@$name) is-invalid
            @enderror" data-target="#{{ @$id?$id:$name }}" data-toggle="datetimepicker" value="{{ $value?$value:'' }}" @if(@$ro) readonly @endif>
        <div class="input-group-append" data-target="#{{ @$id?$id:$name }}" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
        </div>

        @if (Session::has('errors') && @Session::get('errors')->first($name))
        <span class="invalid-feedback" role="alert">
            <strong>{{ Session::get('errors')->first($name) }}</strong>
        </span>
        @endif
    </div>


    @if(@$small)
    <span class="text-muted"><small>{{@$small}}</small></span>
    @endif
</div>