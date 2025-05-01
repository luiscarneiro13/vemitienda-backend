<?php

namespace App\Jobs;

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
        $process = new Process([
            'yt-dlp',
            $this->video->url,
            '-o',
            storage_path('app/public/videos/%(title)s.%(ext)s'),
            '--print-json',
            '--merge-output-format',
            'mp4',
        ]);

        try {
            $process->mustRun();

            $output = json_decode($process->getOutput(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->video->status = 'failed';
            } else {
                $this->video->status = 'completed';
                $this->video->info = $output;

                $this->video->save();
            }
        } catch (Throwable $exception) {
            $this->video->status = 'failed';
            $this->video->save();
            logger(sprintf('Could not download video id %d with url %s', $this->video->id, $this->video->url));

            throw $exception;
        }
    }
}
