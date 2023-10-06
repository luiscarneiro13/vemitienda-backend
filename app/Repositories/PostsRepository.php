<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostsRepository
{
    static function getPosts($limit = 10)
    {
        $filtrar = request()->get('query');

        return Post::query()
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
            'extract' => substr(html_entity_decode(strip_tags($body)), 0, 300).'...',
            'body' => $body,
            'status' => request()->status,
            'user_id' => $user->id,
            'category_id' => request()->category_id
        ];
        $post = Post::create($insert);
        $post->save();
        $post->tags()->attach(request()->tags);

        return $post;
    }

    static function editPost($id)
    {
        return Post::find($id);
    }

    static function updatePost($id)
    {
        $model = Post::find($id);
        $model->name        = request()->name;

        return $model->save();
    }

    static function deletePost($id)
    {
        $model = Post::find($id);
        return $model->delete();
    }
}
