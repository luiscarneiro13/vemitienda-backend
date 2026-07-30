@extends('layouts.public.playlist')

@section('content')
<x-playlist-view :playlist="$playlist" :editable="false" :show-sort="false" :reorder-url="route('playlist.public.reorder', $playlist->slug)" />
@endsection

@section('js')
@include('partials.playlist-scripts', ['reorderUrl' => route('playlist.public.reorder', $playlist->slug)])
@endsection
