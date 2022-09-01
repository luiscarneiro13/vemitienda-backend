<div class="checkbox col-md-{{@$columns}}">
	<label><input type="checkbox" name="{{ @$name }}" class="{{ @$class }}" id="{{ @$id }}" value="{{ @$value }}"  @if(isset($opciones))@if(in_array($id,$opciones)) checked="checked" @endif @endif @if(@$checked>0)checked="checked"@endif/> @if(@$label){{@$label}}@endif </label>
</div>