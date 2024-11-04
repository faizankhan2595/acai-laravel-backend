<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class TransactionResource extends JsonResource
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
            'transaction_type' => $this->transaction_type,
            'transaction_value' => $this->transaction_value,
            'message' => (json_decode($this->data)->message) ? Str::limit(json_decode($this->data)->message,38, '...') : null,
            'sub_heading' => json_decode($this->data)->sub_heading ?? null,
            'date' => $this->created_at->format('d M Y'),

        ];
    }
}
