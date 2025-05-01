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

class DownloadMp3PROD implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    /**
     * @var Video
     */
    private $video;
    private $cookies;

    /**
     * Create a new job instance.
     *
     * @param Video $video
     * @param string $cookies
     */
    public function __construct(Video $video, $cookies)
    {
        $this->video = $video;
        $this->cookies = $cookies;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        event(new InicioDescarga("MP3"));
        event(new InicioDescarga("1.- cookiesPath"));
        $cookiesPath = public_path('cookies/cookies.txt'); // Ruta temporal para cookies
        event(new DescargaFallida($cookiesPath));
        event(new InicioDescarga("2.- cookiesPath"));

        // Guardar las cookies en un archivo si existen
        if (!empty($this->cookies)) {
            event(new InicioDescarga("3.- cookiesPath"));
            file_put_contents($cookiesPath, $this->cookies);
        }

        event(new InicioDescarga("4.- cookiesPath"));
        // Construir el comando yt-dlp
        $command = [
            'yt-dlp',
            $this->video->url,
            '-o',
            public_path('videos-yt/%(title)s.%(ext)s'),
            '--print-json',
            '-x',
            '--extract-audio',
            '--audio-format',
            'mp3',
        ];
        event(new InicioDescarga("5.- cookiesPath"));

        // Si hay cookies, agregar la opción --cookies
        if (file_exists($cookiesPath)) {
            event(new InicioDescarga("6.- cookiesPath"));
            array_splice($command, 1, 0, ['--cookies', $cookiesPath]);
        }

        event(new InicioDescarga("7.- cookiesPath"));
        $process = new Process($command);
        event(new InicioDescarga("8.- cookiesPath"));

        try {
            event(new InicioDescarga("9.- cookiesPath"));
            $process->mustRun();
            event(new InicioDescarga("10.- cookiesPath"));
            $output = json_decode($process->getOutput(), true);
            event(new InicioDescarga("11.- cookiesPath"));

            if (json_last_error() !== JSON_ERROR_NONE) {
                event(new InicioDescarga("12.- cookiesPath"));
                event(new DescargaFallida("Error"));
                $this->video->status = 'failed';
            } else {
                event(new InicioDescarga("13.- cookiesPath"));
                event(new DescargaExitosa("Descarga completa"));
                $this->video->status = 'completed';
                $this->video->info = $output;
                $this->video->save();
            }
            event(new InicioDescarga("14.- cookiesPath"));

            // Eliminar el archivo de cookies después de su uso
            if (file_exists($cookiesPath)) {
                unlink($cookiesPath);
            }
        } catch (Throwable $exception) {
            event(new InicioDescarga("15.- cookiesPath"));
            event(new DescargaFallida("Error"));
            $this->video->status = 'failed';
            $this->video->save();
            logger(sprintf('Error al descargar el video id %d con url %s', $this->video->id, $this->video->url));

            throw $exception;
        }
        event(new InicioDescarga("16.- cookiesPath"));
    }
}
