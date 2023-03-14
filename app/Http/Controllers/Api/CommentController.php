<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required',
            'comment' => 'required',
        ]);

        try {
            $comment = new Comment;

            $comment->post_id = $request->input('post_id');
            $comment->user_id = auth()->user()->id;
            $comment->text = $request->input('comment');

            $comment->save();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $comment = Comment::find($id);
            $comment->delete();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

    }
}
