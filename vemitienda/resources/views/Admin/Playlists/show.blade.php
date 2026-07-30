@extends('layouts.adminlte.index')
@section('content')

<x-playlist-view :playlist="$playlist" :available-metronomes="$availableMetronomes" :editable="true"
    :reorder-url="route('playlists.reorder', $playlist->id)" :back-url="route('playlists.index')" />

@endsection

@section('js')
@include('partials.playlist-scripts', ['reorderUrl' => route('playlists.reorder', $playlist->id)])
@endsection
