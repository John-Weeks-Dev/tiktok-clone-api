<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UsersCollection;
use App\Models\User;

class GlobalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getRandomUsers()
    {
        try {
            $randomUsers = User::inRandomOrder()->limit(10)->get();

            $suggested = User::inRandomOrder()->limit(5)->get();

            return response()->json([
                'suggested' => new UsersCollection($suggested),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
