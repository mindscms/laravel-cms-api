<?php

namespace App\Http\Resources\Users;

use App\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersTagsResource extends JsonResource
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
            'id'            => $this->id,
            'name'          => $this->name,
        ];
    }
}
