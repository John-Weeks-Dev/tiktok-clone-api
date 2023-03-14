<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AllPostsCollection;
use App\Http\Resources\UsersCollection;
use App\Models\Post;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $posts = Post::where('user_id', $id)->orderBy('created_at', 'desc')->get();
            $user = User::where('id', $id)->get();

            return response()->json([
                'posts' => new AllPostsCollection($posts),
                'user' => new UsersCollection($user)
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
