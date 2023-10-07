@extends('layouts.adminlte.index')
@section('content')
    <x-TablaDatos :data="@$data" resource='blog' edit="true"/>
@endsection
