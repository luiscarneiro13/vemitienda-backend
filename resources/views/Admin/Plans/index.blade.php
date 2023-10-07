@extends('layouts.adminlte.index')
@section('content')

<x-Grilla :data="@$data" resource='plans' edit="true"/>

@endsection
