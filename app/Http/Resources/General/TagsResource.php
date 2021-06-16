<?php

namespace App\Http\Resources\General;

use App\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;

class TagsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name'          => $this->name,
            'slug'          => $this->slug,
            'url'           => route('frontend.tag.posts', $this->slug),
            'posts_count'   => $this->posts->count(),
        ];
    }
}
