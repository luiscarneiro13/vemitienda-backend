<div class="col-md-{{@$columns}}">
    <div class="form-group">
        @if(@$label)
        <label class="control-label">{{@$label}} @if(@$required) <span class="text-danger">*</span> @endif</label>
        @endif
        <select id="{{@$id?$id:$name}}" name="{{@$name}}" class="form-control {{@$class?$class:''}} chosen-container chosen-container-single @error(@$name) is-invalid @enderror">
            <option value="">{{@$optionValue?$optionValue:'Seleccione una opción'}}</option>
            @if(@$datos)
            @foreach ($datos as $item)
            @php
            if(!$item->id){
            $id_control=$item->label;
            }else{
            $id_control=$item->id;
            }
            @endphp
            <option value="{{$id_control}}" @if(@$selected) @if($selected==$id_control) selected="selected" @endif
                @endif>{{$item->label}}</option>
            @endforeach
            @endif
        </select>

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