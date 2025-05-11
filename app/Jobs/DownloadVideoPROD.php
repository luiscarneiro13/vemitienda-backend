<?php

namespace App\Jobs;

use App\Events\DescargaExitosa;
use App\Events\DescargaFallida;
use App\Events\InicioDescarga;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;
use Throwable;

class DownloadVideoPROD implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    /**
     * @var Video
     */
    private $video;

    /**
     * Create a new job instance.
     *
     * @param Video $video
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
        event(new InicioDescarga("Inicio de descarga"));

=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
        try {
            $nombre = uniqid() . 'mp4';
            $ruta_video = $this->downloadVideo($this->video->url, $nombre);

            if (!$ruta_video) {
                event(new DescargaFallida("Error"));
                $this->video->status = 'failed';
            } else {
                event(new DescargaExitosa("Descarga completa"));
                $this->video->status = 'completed';
                $this->video->info = $ruta_video;
                $this->video->save();
            }
        } catch (Throwable $exception) {
            event(new DescargaFallida("Error"));
            $this->video->status = 'failed';
            $this->video->save();
            logger(sprintf('Error al descargar el video id %d con url %s', $this->video->id, $this->video->url));

            throw $exception;
        }
    }

    public function downloadVideo($url, $nombre)
    {
        // Comando para iniciar el contenedor con la URL y el nombre
        $command = "docker-compose --profile manual run --rm youtube_downloader -e VIDEO_URL=\"$url\" -e VIDEO_NAME=\"$nombre\"";

        // Ejecutar el comando y capturar la salida
        $output = shell_exec($command);

        // Buscar la ruta del archivo descargado en la salida
        preg_match('/Descarga completada: (.+\.mp4)/', $output, $matches);

        // Retornar la ruta del archivo si fue descargado correctamente
        return isset($matches[1]) ? $matches[1] : null;
    }
}
