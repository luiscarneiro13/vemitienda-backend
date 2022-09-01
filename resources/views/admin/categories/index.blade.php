@extends('layouts.adminlte.index')
@section('content')
<x-tablaDatos resource="categories" :data="@$data" title="Categorías"/>

@endsection