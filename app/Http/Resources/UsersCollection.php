<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UsersCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {
        return $this->collection->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'bio' => $user->bio,
                'image' => url('/') . $user->image,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];
        });
    }
}
