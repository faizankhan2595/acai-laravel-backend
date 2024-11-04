<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {;
        return [
            'title' => ($this->data['title']) ? $this->data['title'] : NULL,
            'message' => ($this->data['message']) ? $this->data['message'] : NULL,
            'is_read' => (!is_null($this->read_at)) ? 1 : 0,
            'date' => (!is_null($this->created_at)) ? $this->created_at->diffForHumans() : NULL,
            'time' => (!is_null($this->created_at)) ? $this->created_at->format('g:i A') : NULL,
            'page' => $this->currentPage(),
        ];
    }
}
