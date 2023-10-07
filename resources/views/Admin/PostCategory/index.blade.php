@extends('layouts.adminlte.index')
@section('content')

<x-Grilla :data="@$data" resource='postcategory' edit="true"/>

@endsection
