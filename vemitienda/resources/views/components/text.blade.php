<div class="col-md-{{@$columns}}">
    <div class="form-group">
        <label class="control-label">{{@$label}} @if(@$required) <span class="text-danger">*</span> @endif</label>

        <input id="{{@$id?$id:$name}}" name="{{@$name}}" type="{{@$type?$type:'text'}}" @if(@$type=="number") @if(@$min)  min="{{@$min}}" @else min="0" @endif @if(@$max)  max="{{@$max}}" @else max="2000" @endif value="{{@!$value? 0:@$value}}"  @else  value="{{@$value?$value:old(@$name)}}" @endif 
            class="form-control {{@$class?$class:''}} @error(@$name) is-invalid @enderror"
            placeholder="{{@$placeholder}}" @if(@$disabled) disabled="true"
            @endif   @if(@$readonly) readonly
            @endif/>

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