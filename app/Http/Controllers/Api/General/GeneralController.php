<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\General\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class GeneralController extends Controller
{

    public function get_posts()
    {
        $posts = Post::whereHas('category', function ($query) {
                $query->whereStatus(1);
            })
            ->whereHas('user', function ($query) {
                $query->whereStatus(1);
            })
            ->post()->active()->orderBy('id', 'desc')->paginate(5);

        if ($posts->count() > 0) {
            return PostResource::collection($posts);
        } else {
            return response()->json(['error' => true, 'message'=> 'No posts found'], 201);
        }

    }

}
