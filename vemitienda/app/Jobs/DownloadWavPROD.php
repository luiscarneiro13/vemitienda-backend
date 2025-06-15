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

    /**
     * Create a new job instance.
     *
     * @param Video $video
     * @param string $cookies
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

        // Construir el comando yt-dlp
        $command = [
            'yt-dlp',
            '--cookies',
            public_path('videosyt/cookies.txt'), // Cargar cookies guardadas
            $this->video->url,
            '-o',
            public_path('videosyt/%(title)s.%(ext)s'), // La carpeta no puede tener caracteres especiales por eso se llama videosyt asi pegado
            '--print-json',
            '-x',
            '--extract-audio',
            '--audio-format',
            'wav',
        ];

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
        } catch (Throwable $exception) {
            event(new DescargaFallida("Error"));
            $this->video->status = 'failed';
            $this->video->save();
            logger(sprintf('Error al descargar el video id %d con url %s', $this->video->id, $this->video->url));

            throw $exception;
        }
    }
}
