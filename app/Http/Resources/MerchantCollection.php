<?php

namespace App\Http\Resources;

use App\Http\Resources\MerchantResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MerchantCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
       return MerchantResource::collection($this->collection);
    }
}
