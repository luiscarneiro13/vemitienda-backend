<div class="col-md-{{@$columns?$columns:'12'}}">
    <label class="control-label">{{@$label}} @if(@$required) <span class="text-danger">*</span> @endif</label>
    <div class="input-group date">
        <input id="{{@$id?$id:$name}}" name="{{ $name }}" type="text" class="form-control {{@$class?$class:''}}" value="{{@$value?$value:''}}">
        <span class="input-group-addon"><i class="demo-pli-calendar-4"></i></span>
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