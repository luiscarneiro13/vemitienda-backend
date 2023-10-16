@extends('layouts.adminlte.index')
@section('content')

<x-Grilla :data="@$data" resource='versions' edit="true"/>

@endsection
