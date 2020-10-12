<?php

namespace App\Http\Resources\General;

use Illuminate\Http\Resources\Json\JsonResource;

class PostsMediaResource extends JsonResource
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
            'file_name'     => $this->file_name,
            'file_type'     => $this->file_type,
            'file_size'     => $this->file_size,
            'url'           => asset('assets/posts/' . $this->file_name),
        ];
    }
}
