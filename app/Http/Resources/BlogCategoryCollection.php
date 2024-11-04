<?php

namespace App\Http\Resources;

use App\Http\Resources\BlogCategoryResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BlogCategoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return BlogCategoryResource::collection($this->collection);
    }
}
