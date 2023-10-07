<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    const LATESTS = 3;

    public function index()
    {
        $query = request()->input('query');
        $categoria = request()->input('categoria');
        $etiqueta = request()->input('etiqueta');

        $posts = Post::when($query, function ($q) use ($query) {
            $q->where('name', 'like', '%' . $query . '%');
        })
            ->when($categoria, function ($q) use ($categoria) {
                $q->whereHas('category', function ($query) use ($categoria) {
                    $query->where('name', $categoria);
                });
            })
            ->when($etiqueta, function ($q) use ($etiqueta) {
                $q->whereHas('tags', function ($query) use ($etiqueta) {
                    $query->where('name', $etiqueta);
                });
            })
            ->orderBy('id', 'desc')->paginate(9);

        $datos['datos'] = $posts;

        $datos['categories'] = PostCategory::whereHas('posts')->withCount('posts')->get();
        $datos['tags'] = Tag::whereHas('posts')->get();
        $datos['latests'] = Post::orderBy('id', 'desc')->limit(self::LATESTS)->get();
        // return $datos['categories'];
        return view('blog', $datos);
    }

    public function show($slug)
    {
        $datos['post'] = Post::where('slug', $slug)->first();
        if(!$datos['post']){
            return redirect(url('blog'));
        }
        $datos['categories'] = PostCategory::whereHas('posts')->withCount('posts')->get();
        $datos['tags'] = Tag::whereHas('posts')->get();
        $datos['latests'] = Post::orderBy('id', 'desc')->limit(self::LATESTS)->get();
        return view('blogDetail', $datos);
    }
}
