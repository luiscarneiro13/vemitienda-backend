@extends('layouts.adminlte.index')
@section('content')
    <x-Grilla :data="@$data" resource='blog' edit="true"/>
@endsection
