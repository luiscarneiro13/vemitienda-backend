<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WEB\PostCategoryRequest;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datos['infoData'] = PostCategory::orderBy('id', 'desc')->paginate(9);

        $datos['nombreColumnas'] = collect([
            'Nombre' => 'name',
            'Cantidad' => 'quantity'
        ]);

        $datos['token'] = csrf_token();

        $data['data'] = $datos;

        return view('Admin.PostCategory.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Admin.PostCategory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostCategoryRequest $request)
    {
        $insert = request()->all();
        $insert['slug'] = Str::slug(request()->name, '-');
        $category = PostCategory::create($insert);
        $category->save();
        return redirect()->route('postcategory.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('Admin.PostCategory.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['cat'] = PostCategory::find($id);
        return view('Admin.PostCategory.edit', $data);
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
        $cat = PostCategory::find($id);
        $cat->name     = request()->name;
        $cat->slug = Str::slug(request()->name, '-');
        $cat->save();
        return redirect()->route('postcategory.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cat = PostCategory::find($id);
        $cat->delete();

        return redirect()->route('postcategory.index');
    }
}
