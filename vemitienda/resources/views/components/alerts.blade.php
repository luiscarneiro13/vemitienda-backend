
<style>
    .alert-success {
        background-color: #1B998B;
        border-color: #1B998B;
    }
</style>


@if(Session::has('mensaje'))
<div class="alert alert-{{Session::get('color')?Session::get('color'):'success'}}">
    <button class="close" data-dismiss="alert"><i class="pci-cross pci-circle"></i></button>
    <i class="fa fa-check-circle">&nbsp;</i>{{Session::get('mensaje')}}
</div>
@endif