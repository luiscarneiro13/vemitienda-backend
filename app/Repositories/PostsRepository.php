<?php

namespace App\Repositories;

use App\Http\Controllers\API\V3\ImagesController;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostsRepository
{
    static function getPosts($limit = 10)
    {
        $filtrar = request()->get('query');

        return Post::with('image')->query()
            ->when($filtrar, function ($q) use ($filtrar) {
                $q->where('name', 'like', '%' . $filtrar . '%');
                return $q;
            })->paginate($limit);
    }

    static function storePost($web = false)
    {
        $name = request()->name;
        $body = request()->body;
        $user = Auth::user();

        $insert = [
            'name' => $name,
            'slug' => Str::slug($name, '-'),
            'extract' => substr(html_entity_decode(strip_tags($body)), 0, 300) . '...',
            'body' => $body,
            'status' => request()->status,
            'user_id' => $user->id,
            'category_id' => request()->category_id
        ];
        $post = Post::create($insert);
        $post->save();
        $post->tags()->attach(request()->tags);

        if (request()->file('image')) {
            $image = new ImagesController();
            $image->storeImagePost(request()->file('image'), $post->id);
        }

        return $post;
    }

    static function editPost($id)
    {
        return Post::with('image')->find($id);
    }

    static function updatePost($id)
    {
        $name = request()->name;
        $body = request()->body;
        $user = Auth::user();

        $post = Post::find($id);
        $post->name = $name;
        $post->slug = Str::slug($name, '-');
        $post->extract = substr(html_entity_decode(strip_tags($body)), 0, 300) . '...';
        $post->body = $body;
        $post->status = request()->status;
        $post->user_id = $user->id;
        $post->category_id = request()->category_id;
        $post->save();
        $post->tags()->detach();
        $post->tags()->attach(request()->tags);

        if (request()->file('image')) {
            $image = new ImagesController();
            $image->storeImagePost(request()->file('image'), $post->id);
        }

        return $post;
    }

    static function deletePost($id)
    {
        $model = Post::find($id);
        return $model->delete();
    }
}
