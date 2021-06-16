<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\General\PostResource;
use App\Http\Resources\General\PostsResource;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\NewCommentForAdminNotify;
use App\Notifications\NewCommentForPostOwnerNotify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Purify\Facades\Purify;

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
            return PostsResource::collection($posts);
        } else {
            return response()->json(['error' => true, 'message'=> 'No posts found'], 201);
        }

    }

    public function search(Request $request)
    {
        $keyword = isset($request->keyword) && $request->keyword != '' ? $request->keyword : null;

        $posts = Post::with(['media', 'user', 'tags'])
            ->whereHas('category', function ($query) {
                $query->whereStatus(1);
            })
            ->whereHas('user', function ($query) {
                $query->whereStatus(1);
            });

        if ($keyword != null) {
            $posts = $posts->search($keyword, null, true);
        }

        $posts = $posts->post()->active()->orderBy('id', 'desc')->get();

        if ($posts->count() > 0) {
            return PostsResource::collection($posts);
        } else {
            return response()->json(['error' => true, 'message'=> 'No posts found'], 201);
        }
    }

    public function category($slug)
    {
        $category = Category::whereSlug($slug)->whereStatus(1)->first();

        if ($category) {
            $posts = Post::with(['media', 'user', 'tags'])
                ->whereCategoryId($category->id)
                ->post()
                ->active()
                ->orderBy('id', 'desc')
                ->get();

            if ($posts->count() > 0) {
                return PostsResource::collection($posts);
            } else {
                return response()->json(['error' => true, 'message'=> 'No posts found'], 201);
            }
        }

        return response()->json(['error' => true, 'message'=> 'Something was wrong'], 201);
    }

    public function tag($slug)
    {
        $tag = Tag::whereSlug($slug)->first()->id;

        if ($tag) {
            $posts = Post::with(['media', 'user', 'tags'])
                ->whereHas('tags', function ($query) use ($slug) {
                    $query->where('slug', $slug);
                })
                ->post()
                ->active()
                ->orderBy('id', 'desc')
                ->get();

            if ($posts->count() > 0) {
                return PostsResource::collection($posts);
            } else {
                return response()->json(['error' => true, 'message'=> 'No posts found'], 201);
            }
        }

        return response()->json(['error' => true, 'message'=> 'Something was wrong'], 201);
    }

    public function archive($date)
    {
        $exploded_date = explode('-', $date);
        $month = $exploded_date[0];
        $year = $exploded_date[1];

        $posts = Post::with(['media', 'user', 'tags'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->post()
            ->active()
            ->orderBy('id', 'desc')
            ->get();

        if ($posts->count() > 0) {
            return PostsResource::collection($posts);
        } else {
            return response()->json(['error' => true, 'message'=> 'Something was wrong'], 201);
        }

    }

    public function author($username)
    {
        $user = User::whereUsername($username)->whereStatus(1)->first();

        if ($user) {
            $posts = Post::with(['media', 'user', 'tags'])
                ->whereUserId($user->id)
                ->post()
                ->active()
                ->orderBy('id', 'desc')
                ->get();

            if ($posts->count() > 0) {
                return PostsResource::collection($posts);
            } else {
                return response()->json(['error' => true, 'message'=> 'No posts found'], 201);
            }
        }

        return response()->json(['error' => true, 'message'=> 'Something was wrong'], 201);
    }

    public function show_post($slug)
    {
        $post = Post::with(['category', 'media', 'user', 'tags',
            'approved_comments' => function($query) {
                $query->orderBy('id', 'desc');
            }
        ]);

        $post = $post->whereHas('category', function ($query) {
            $query->whereStatus(1);
        })
            ->whereHas('user', function ($query) {
                $query->whereStatus(1);
            });

        $post = $post->whereSlug($slug);
        $post = $post->active()->post()->first();

        if($post) {

            return new PostResource($post);
        } else {
            return response()->json(['error' => true, 'message' => 'No post found'], 201);
        }
    }

    public function store_comment(Request $request, $slug)
    {
        $validation = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|email',
            'url'       => 'nullable|url',
            'comment'   => 'required|min:10',
        ]);
        if ($validation->fails()){
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $post = Post::whereSlug($slug)->wherePostType('post')->whereStatus(1)->first();
        if ($post) {

            $userId                 = auth()->check() ? auth()->id() : null;
            $data['name']           = $request->name;
            $data['email']          = $request->email;
            $data['url']            = $request->url;
            $data['ip_address']     = $request->ip();
            $data['comment']        = Purify::clean($request->comment);
            $data['post_id']        = $post->id;
            $data['user_id']        = $userId;

            $comment = $post->comments()->create($data);

            if (auth()->guest() || auth()->id() != $post->user_id) {
                $post->user->notify(new NewCommentForPostOwnerNotify($comment));
            }

            User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'editor']);
            })->each(function ($admin, $key) use ($comment) {
                $admin->notify(new NewCommentForAdminNotify($comment));
            });

            return response()->json(['errors' => false, 'message' => 'Comment added successfully'], 200);
        }

        return response()->json(['errors' => true, 'message' => 'Something was wrong'], 200);

    }


    public function do_contact(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|email',
            'mobile'    => 'nullable|numeric',
            'title'     => 'required|min:5',
            'message'   => 'required|min:10',
        ]);
        if ($validation->fails()){
            return response()->json(['errors' => true, 'message' => $validation->errors()], 200);
        }

        $data['name']       = $request->name;
        $data['email']      = $request->email;
        $data['mobile']     = $request->mobile;
        $data['title']      = $request->title;
        $data['message']    = $request->message;

        Contact::create($data);

        return response()->json(['errors' => false, 'message' => 'Message sent successfully'], 200);

    }

}
