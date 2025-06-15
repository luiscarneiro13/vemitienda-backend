<div class="col-md-{{@$columns}}">
    <div class="form-group">
        <label class="control-label">{{@$label}} @if(@$required) <span class="text-danger">*</span> @endif</label>

        <div class="input-group date">
            <input id="{{@$id}}" type="text" name="{{ $name }}" class="form-control"
                value="{{@$value?$value:old(@$name)}}" placeholder="{{@$placeholder}}">

            <span class="input-group-addon"><i class="demo-pli-clock"></i></span>
        </div>

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