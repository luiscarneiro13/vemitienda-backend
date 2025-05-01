@extends('videos.base')
@section('title', 'Video Downloader')

@section('content')

    <form action="{{ route('prepare') }}" method="post">

        @csrf

        <div class="form-group">
            <input name="url" type="text" required class="form-control @error('url') is-invalid @enderror" id="url"
                aria-describedby="url" value="{{ old('url') }}" autocomplete="off" autofocus>
        </div>

        <div class="form-group">
            <label for="format">Formato de descarga:</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="format" id="mp4" value="mp4" checked>
                <label class="form-check-label" for="mp4">MP4</label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="format" id="wav" value="wav">
                <label class="form-check-label" for="wav">WAV</label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="format" id="mp3" value="mp3">
                <label class="form-check-label" for="mp3">MP3</label>
            </div>
        </div>

        <div class="text-center">
            <div id="buttonContainer">
                <button type="submit" class="btn btn-lg btn-primary">Descargar</button>
            </div>
        </div>

        <div class="text-center">
            <span id="downloadMessage"></span>
        </div>
    </form>

@endsection
