<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $posts = Post::factory(30)->create();

        foreach ($posts as $post) {
            Image::create([
                'url' => 'images/64e392a1de99464e392a1de997.webp',
                'thumbnail' => 'images/64e392a1de99464e392a1de997.webp',
                'imageable_id' => $post->id,
                'imageable_type' => Post::class
            ]);
            $post->tags()->attach([rand(1, 4), rand(5, 8)]);
        }
    }
}
