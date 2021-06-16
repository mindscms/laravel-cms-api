<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;

class UsersPostCommentsResource extends JsonResource
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
            'url'           => $this->url,
            'comment'       => $this->comment,
            'status'        => $this->status,
            'status_text'   => $this->status(),
            'author_type'   => $this->user_id != '' ? 'Member' : 'Guest',
            'create_date'   => $this->created_at->format('d-m-Y h:i a'),
        ];
    }
}
