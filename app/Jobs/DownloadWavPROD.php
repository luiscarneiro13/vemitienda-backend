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

class DownloadWavPROD implements ShouldQueue
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
        event(new InicioDescarga("WAV"));
        $cookiesPath = public_path('cookies.txt'); // Ruta temporal para cookies
        event(new DescargaFallida($cookiesPath));
        // Guardar cookies en un archivo si existen
        if (!empty($this->cookies)) {
            file_put_contents($cookiesPath, $this->cookies);
        }

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
            'wav',
        ];

        // Si hay cookies, agregar la opción --cookies
        if (file_exists($cookiesPath)) {
            array_splice($command, 1, 0, ['--cookies', $cookiesPath]);
        }

        $process = new Process($command);

        try {
            $process->mustRun();
            $output = json_decode($process->getOutput(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                event(new DescargaFallida("Error"));
                $this->video->status = 'failed';
            } else {
                event(new DescargaExitosa("Descarga completa"));
                $this->video->status = 'completed';
                $this->video->info = $output;
                $this->video->save();
            }

            // Eliminar el archivo de cookies después de su uso
            if (file_exists($cookiesPath)) {
                unlink($cookiesPath);
            }
        } catch (Throwable $exception) {
            event(new DescargaFallida("Error"));
            $this->video->status = 'failed';
            $this->video->save();
            logger(sprintf('Error al descargar el video id %d con url %s', $this->video->id, $this->video->url));

            throw $exception;
        }
    }
}
