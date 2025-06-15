@extends('layouts.adminlte.index')
@section('content')

<x-Grilla :data="@$data" resource='services' edit="true" />

@endsection
