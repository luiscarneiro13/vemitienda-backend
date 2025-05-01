@extends('videos.base')

@section('content')
    <div class="text-center">
        <div id="loadingIndicator" style="display: block;">
            <div style="padding-top:125.326%;position:relative;">
                <iframe src="https://gifer.com/embed/XOsX" width="100%" height="100%" style='position:absolute;top:0;left:0;' frameBorder="0" allowFullScreen></iframe>
            </div>
            <p><a href="https://gifer.com">a través de GIFER</a></p>
        </div>
    </div>

    <div class="text-center">
        <span id="downloadMessage"></span>
    </div>

    <script src="https://cdn.ably.io/lib/ably.min-1.js"></script>

    <script>
        const ably = new Ably.Realtime('T4up8w.Jqabrg:b9hXo2goM6TrI8Ra5edvfGYTD1Pp2badcw7z-C6-PHI');

        ably.connection.on('connected', function() {
            console.log("✅ Conectado a Ably correctamente");
        });

        ably.connection.on('failed', function(stateChange) {
            console.error("❌ Error de conexión con Ably:", stateChange);
        });

        // Ably internamente le coloca public, por eso el canal se debe llamar public:canal-chat
        const channel = ably.channels.get('public:canal-chat');

        channel.subscribe("inicio.descarga", function() {
            document.getElementById("loadingIndicator").style.display = "block";
        });

        channel.subscribe("descarga.exitosa", function(message) {
            document.getElementById("loadingIndicator").style.display = "none";
            document.getElementById("downloadMessage").innerHTML =
                `<a href="${message.data}" target="_blank">Descargar archivo</a>`;
        });

        channel.subscribe("descarga.fallida", function(message) {
            document.getElementById("loadingIndicator").style.display = "none";
            document.getElementById("downloadMessage").innerHTML =
                `<span class="text-danger">${message.data}</span>`;
        });
    </script>
@endsection
