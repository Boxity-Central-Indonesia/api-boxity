<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\PostsCategories;

class PostCategoryController extends Controller
{
    public function store(Request $request)
    {
        $comment = PostsCategories::create($request->all());
        return response()->json($comment, 201);
    }

    public function update(Request $request, $id)
    {
        $comment = PostsCategories::findOrFail($id);
        $comment->update($request->all());
        return response()->json($comment);
    }

    public function destroy($id)
    {
        $comment = PostsCategories::findOrFail($id);
        $comment->delete();
        return response()->json(null, 204);
    }
    public function index()
    {
        $comments = PostsCategories::get();
        return response()->json($comments);
    }

    // Menampilkan komentar berdasarkan ID
    public function show($id)
    {
        $comment = PostsCategories::findOrFail($id);
        return response()->json($comment);
    }
}
