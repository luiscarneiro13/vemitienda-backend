@extends('layouts.adminlte.index')
@section('content')
<x-tablaDatos resource="users" :data="@$data" title="Usuarios"/>

@endsection