<?php

namespace App\Services;

class DownloadVideo
{

    public function descargarVideo($url, $nombre)
    {
        // Comando para iniciar el contenedor con la URL y el nombre
        $command = "docker-compose --profile manual run --rm youtube_downloader -e VIDEO_URL=\"$url\" -e VIDEO_NAME=\"$nombre\"";

        // Ejecutar el comando y capturar la salida
        $output = shell_exec($command);

        // Buscar la ruta del archivo descargado en la salida
        preg_match('/Descarga completada: (.+\.mp4)/', $output, $matches);

        // Retornar la ruta del archivo si fue descargado correctamente
        return isset($matches[1]) ? $matches[1] : "Error al descargar el video";
    }
}
