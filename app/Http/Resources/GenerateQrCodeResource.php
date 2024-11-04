<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class GenerateQrCodeResource extends JsonResource
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
            'code' => $this->code,
            'qr_path' => url('/').Storage::url($this->qr_path),
            'amount' => config('admin.default_currency').$this->amount,
            'date' => $this->created_at->format('d M Y'),
            'time' => $this->created_at->format('g:i A'),
        ];
    }
}
