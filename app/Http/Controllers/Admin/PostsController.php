<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use App\Repositories\PostsRepository;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $filtrar = request()->get('query');

        $datos['infoData'] = Post::with('image')->orderBy('id', 'desc')->paginate(9);
        $datos['nombreColumnas'] = collect([
            'id' => 'id',
            'Imagen'=>'image',
            'Nombre' => 'name',
        ]);

        $datos['token'] = csrf_token();

        $data['data'] = $datos;

        return view('Admin.Blog.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['categories'] = PostCategory::select('id', 'name as label')->get();
        $data['tags'] = Tag::select('id', 'name as label')->get();
        $data['status'] = [['id' => '1', 'label' => "No"], ['id' => '2', 'label' => "Si"]];
        return view('Admin.Blog.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            PostsRepository::storePost(request()->all());
            return redirect()->route('blog.index');
        } catch (\Throwable $th) {
            return $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['categories'] = PostCategory::select('id', 'name as label')->get();
        $data['tags'] = Tag::select('id', 'name as label')->get();
        $data['status'] = [['id' => '1', 'label' => "No"], ['id' => '2', 'label' => "Si"]];
        $data['blog'] = Post::with('tags','image')->find($id);
        $data['tagSelected'] = $data['blog']->tags->pluck('id')->toArray();
        // return $data['tagSelected'];
        return view('Admin.Blog.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            PostsRepository::updatePost($id);
            return redirect()->route('blog.index');
        } catch (\Throwable $th) {
            return $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Post::find($id);
        $user->delete();

        return redirect()->route('blog.index');
    }
}
