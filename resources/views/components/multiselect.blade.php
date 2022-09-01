<div class="col-md-{{@$columns}}">
    <div class="form-group">
        @if(@$label)
        <label class="control-label">{{@$label}} @if(@$required) <span class="text-danger">*</span> @endif</label>
        @endif
        <select id="{{@$id?$id:$name}}" @if(@$multiple) multiple="" @endif name="{{@$name}}" @if(@$placeholder) data-placeholder="{{ $placeholder }}" @endif
            class="form-control {{@$class?$class:''}}  @error(@$name) is-invalid @enderror chosen-container chosen-container-single">
            @if(@$datos)
            @foreach ($datos as $item)
            @php
            if(!$item->id){
            $id_control=$item->label;
            }else{
            $id_control=$item->id;
            }
            @endphp
            <option value="{{$id_control}}" @if(@$selected) @if(in_array($id_control,$selected)) selected="selected" @endif
                @endif>@if(isset($item->label)){{$item->label}}@else{{ $item->nombre }} @endif</option>
            @endforeach
            @endif
        </select>

        @if (Session::has('errors') && @Session::get('errors')->first($name))
        <span class="text-danger">
            <small><strong>{{ Session::get('errors')->first($name) }}</strong></small>
        </span>
        @endif

        @if(@$small)
        <span class="text-muted"><small>{{@$small}}</small></span>
        @endif

    </div>
</div>