@extends('videos.base')
@section('title', 'Video Downloader')

@section('content')
<form id="downloadForm">
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
        <button type="button" id="downloadButton" class="btn btn-lg btn-primary">Descargar</button>
    </div>

    <div class="text-center">
        <span id="downloadMessage"></span>
    </div>
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
<script src="https://cdn.ably.io/lib/ably.min-1.js"></script>

<script>
    const ably = new Ably.Realtime('T4up8w.Jqabrg:b9hXo2goM6TrI8Ra5edvfGYTD1Pp2badcw7z-C6-PHI');

    ably.connection.on('connected', function () {
        console.log("✅ Conectado a Ably correctamente");
    });

    ably.connection.on('failed', function (stateChange) {
        console.error("❌ Error de conexión con Ably:", stateChange);
    });

    // Ably internamente le coloca public, por eso el canal se debe llamar public:canal-chat
    const channel = ably.channels.get('public:canal-chat');

    channel.subscribe("inicio.descarga", function () {
        document.getElementById("downloadButton").style.display = "none";
        document.getElementById("loadingIndicator").style.display = "block";
    });

    channel.subscribe("descarga.exitosa", function (message) {
        document.getElementById("loadingIndicator").style.display = "none";
        document.getElementById("downloadMessage").innerHTML = `<a href="${message.data}" target="_blank">Descargar archivo</a>`;
    });

    channel.subscribe("descarga.fallida", function (message) {
        document.getElementById("loadingIndicator").style.display = "none";
        document.getElementById("downloadMessage").innerHTML = `<span class="text-danger">${message.data}</span>`;
    });

    document.getElementById("downloadButton").addEventListener("click", function () {
        let button = this;
        let messageContainer = document.getElementById("downloadMessage");

        // Reemplazar el botón por el spinner
        button.outerHTML = `
                <button class="btn btn-lg btn-primary" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Descargando...
                </button>
            `;

        let formData = new FormData(document.getElementById("downloadForm"));

        axios.post("{{ route('prepare') }}", formData)
            .then(response => {
                messageContainer.innerHTML = `<a href="${response.data}" target="_blank">Descargar archivo</a>`;
            })
            .catch(error => {
                messageContainer.innerHTML = `<span class="text-danger">Error en la descarga</span>`;
                console.error("❌ Error:", error);
            });
    });
</script>
@endsection
