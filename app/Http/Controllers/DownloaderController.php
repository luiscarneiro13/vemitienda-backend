<?php

namespace App\Http\Controllers;

use App\Events\InicioDescarga;
use App\Jobs\DownloadMp3;
use App\Jobs\DownloadMp3PROD;
use App\Jobs\DownloadVideo;
use App\Jobs\DownloadVideoPROD;
use App\Jobs\DownloadWav;
use App\Jobs\DownloadWavPROD;
use App\Models\Video;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DownloaderController extends Controller
{
    public function index()
    {
        return view('videos.index');
    }

    public function prepare(Request $request)
    {

        $format = $request->input('format');
        $url = $request->input('url');

        // Crear el video y almacenar las cookies
        $video = Video::create([
            'url' => $url,
            'format' => $format,
        ]);

        // Despachar el job correspondiente según el formato
        switch ($format) {
            case 'mp4':
                DownloadVideoPROD::dispatch($video);
                break;
            case 'mp3':
                DownloadMp3PROD::dispatch($video);
                break;
            case 'wav':
                DownloadWavPROD::dispatch($video);
                break;
        }

        return redirect()->route('status', ['video' => $video->id]);
    }

    public function status(Video $video)
    {
        return view('videos.status', ['video' => $video]);
    }

    public function download(Video $video)
    {
        abort_if($video->status !== 'completed', 404);

        return response()->download(storage_path('app/public/videos/' . $video->info['title'] . '.' . $video->info['ext']));
    }
}
