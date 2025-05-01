@extends('videos.base')
@section('title', 'Video Downloader')

@section('content')

    <p>Preparando descarga</p>

    <script src="https://cdn.ably.io/lib/ably.min-1.js"></script>

    <script>
        const ably = new Ably.Realtime('T4up8w.Jqabrg:b9hXo2goM6TrI8Ra5edvfGYTD1Pp2badcw7z-C6-PHI');

        const channel = ably.channels.get('public:canal-chat');

        ably.connection.on('connected', function() {
            console.log("✅ Conectado a Ably correctamente");
        });

        ably.connection.on('failed', function(stateChange) {
            console.error("❌ Error de conexión con Ably:", stateChange);
        });

        channel.subscribe("inicio.descarga", function(message) {
            console.log(message.data)
            document.getElementById("buttonContainer").innerHTML = `
            <button class="btn btn-lg btn-primary" type="button" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Descargando...
            </button>
        `;
        });

        channel.subscribe("descarga.exitosa", function(message) {
            console.log(message.data)
            document.getElementById("downloadMessage").innerHTML =
                `<a href="${message.data}" target="_blank">Descargar archivo</a>`;
        });

        channel.subscribe("descarga.fallida", function(message) {
            console.log(message.data)
            document.getElementById("downloadMessage").innerHTML =
                `<span class="text-danger">${message.data}</span>`;

            // Restaurar el botón original si hay un error
            document.getElementById("buttonContainer").innerHTML = `
            <button type="button" id="downloadButton" class="btn btn-lg btn-primary">Descargar</button>
        `;

            document.getElementById("downloadButton").addEventListener("click", startDownload);
        });
    </script>
@endsection
