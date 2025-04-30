@extends('videos.base')
@section('title', 'Video Downloader')

@section('content')
    <form method="post" action="{{ route('prepare') }}">
        @csrf

        @if (Session::has('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
        @endif

        <div class="form-group">
            <input name="url" type="text" required class="form-control @error('url') is-invalid @enderror" id="url"
                aria-describedby="url" value="{{ old('url') }}" autocomplete="off" autofocus>

            @error('url')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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
            <button class="btn btn-lg btn-primary">Descargar</button>
        </div>
    </form>


    <script src="https://cdn.ably.io/lib/ably.min-1.js"></script>

    <script>
        // Conéctate a Ably con una API key RESTRINGIDA solo para "subscribe"
        const ably = new Ably.Realtime(
            'T4up8w.Jqabrg:b9hXo2goM6TrI8Ra5edvfGYTD1Pp2badcw7z-C6-PHI'); // reemplaza con tu clave

        // Escucha si la conexión fue exitosa
        ably.connection.on('connected', function() {
            console.log("✅ Conectado a Ably correctamente");
        });

        // Escucha errores de conexión
        ably.connection.on('failed', function(stateChange) {
            console.error("❌ Error de conexión con Ably:", stateChange);
        });

        // Suscribirse al canal público "canal-chat". Ably agrega el prefijo public cuando se emite el evento del backend
        const channel = ably.channels.get('public:canal-chat');

        // Escuchar el evento personalizado "mensaje.nuevo"
        channel.subscribe('mensaje.nuevo', function(message) {
            console.log("📦 Evento recibido:", message.data); // inspecciona estructura
        });

    </script>

@endsection
