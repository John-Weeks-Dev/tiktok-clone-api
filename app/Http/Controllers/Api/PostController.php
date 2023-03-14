<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AllPostsCollection;
use App\Models\Post;
use App\Services\FileService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:mp4',
            'text' => 'required'
        ]);

        try {
            $post = new Post;
            $post = (new FileService)->addVideo($post, $request);

            $post->user_id = auth()->user()->id;
            $post->text = $request->input('text');
            $post->save();

            return response()->json(['success' => 'OK'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $post = Post::where('id', $id)->get();
            $posts = Post::where('user_id', $post[0]->user_id)->get();

            $ids = $posts->map(function ($post) {
                return $post->id;
            });

            return response()->json([
                'post' => new AllPostsCollection($post),
                'ids' => $ids
            ], 200);
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
            $post = Post::findOrFail($id);
            if (!is_null($post->video) && file_exists(public_path() . $post->video)) {
                unlink(public_path() . $post->video);
            }
            $post->delete();

            return response()->json(['success' => 'OK'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
