<form id="{{@$form_id?$form_id:''}}" action="{{@route($route)}}" method="post" enctype="multipart/form-data">
    @csrf
    <input name="{{@$name}}" type="file" id="{{@$name}}">
    <br>
    <button id="btnSubir" style="display:none" class="btn btn-primary form-control"><i class="fa fa-upload"></i> Subir archivo seleccionado</button>
</form>