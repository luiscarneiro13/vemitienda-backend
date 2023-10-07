@extends('layouts.adminlte.index')
@section('content')

<x-Grilla :data="@$data" resource='tags' edit="true"/>

@endsection
