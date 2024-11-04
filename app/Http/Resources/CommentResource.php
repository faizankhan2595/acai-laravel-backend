<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CommentResource extends JsonResource
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
            'id' => $this->id,
            'avatar' => ($this->user->avatar != '') ? url('/').Storage::url($this->user->avatar) : null,
            'author' => $this->user->name,
            'comment_body' => $this->comment_body,
            'date' => $this->created_at->format('d M Y')
        ];
    }
}
