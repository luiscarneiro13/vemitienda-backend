@extends('layouts.adminlte.index')
@section('content')

<x-Grilla :data="@$data" resource='paymentmethods' edit="true"/>

@endsection
