<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\PostsComments;
use App\Events\formCreated;

class PostCommentsController extends Controller
{
    public function store(Request $request)
    {
        $comment = PostsComments::create($request->all());
        return response()->json($comment, 201);
    }

    public function update(Request $request, $id)
    {
        $comment = PostsComments::findOrFail($id);
        $comment->update($request->all());
        return response()->json($comment);
    }

    public function destroy($id)
    {
        $comment = PostsComments::findOrFail($id);
        $comment->delete();
        return response()->json(null, 204);
    }
    public function index()
    {
        $comments = PostsComments::with('post')->get();
        return response()->json($comments);
    }

    // Menampilkan komentar berdasarkan ID
    public function show($id)
    {
        $comment = PostsComments::with('post')->findOrFail($id);
        return response()->json($comment);
    }
}
