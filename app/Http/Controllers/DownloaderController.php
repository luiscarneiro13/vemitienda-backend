<?php

namespace App\Http\Controllers;

use App\Jobs\DownloadMp3;
use App\Jobs\DownloadMp3PROD;
use App\Jobs\DownloadVideo;
use App\Jobs\DownloadVideoPROD;
use App\Jobs\DownloadWav;
use App\Jobs\DownloadWavPROD;
use App\Models\Video;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class DownloaderController extends Controller
{
    public function index()
    {
        return view('videos.index');
    }

    public function prepare(Request $request)
    {
        $this->validate($request, [
            'url' => 'required|url',
        ]);

        $format = $request->input('format');

        $video = Video::create([
            'url' => $request->input('url'),
            'format' => $request->input('format'),
        ]);

        switch ($format) {
            case 'mp4':
                DownloadVideo::dispatch($video); // En local
                DownloadVideoPROD::dispatch($video); // En el hosting
                break;
            case 'mp3':
                DownloadMp3::dispatch($video); // En local
                DownloadMp3PROD::dispatch($video);// En el hosting
                break;
            case 'wav':
                DownloadWav::dispatch($video); // En local
                DownloadWavPROD::dispatch($video);// En el hosting
                break;
        }

        // return redirect()->route('status', ['video' => $video->id]);
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
