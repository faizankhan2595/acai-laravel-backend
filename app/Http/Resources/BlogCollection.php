<?php

namespace App\Http\Resources;

use App\Http\Resources\BlogResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BlogCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return BlogResource::collection($this->collection);
    }
}
