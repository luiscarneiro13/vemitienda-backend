@extends('layouts.adminlte.index')
@section('content')
<h6 class="text-primary">Las empresas no se pueden agregar desde aquí, ellas se deben registrar para corroborar que el email no es falso</h6>
<x-tablaDatos resource="companies" :data="@$data" title="Empresas" hideAdd="true" />

@endsection