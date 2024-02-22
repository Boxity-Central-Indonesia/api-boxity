<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Posts;

class PostController extends Controller
{
    public function index()
    {
        $posts = Posts::with(['user', 'comments'])->get();
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('public/covers');
            $data['cover_image'] = $path;
        }

        $post = Posts::create($data);
        return response()->json($post, 201);
    }

    public function show($id)
    {
        $post = Posts::with(['user', 'comments'])->findOrFail($id);
        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        $post = Posts::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('public/covers');
            $data['cover_image'] = $path;
        }

        $post->update($data);
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Posts::findOrFail($id);
        $post->delete();
        return response()->json(null, 204);
    }
}
