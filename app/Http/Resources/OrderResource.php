<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OrderResource extends JsonResource
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
            'order_id' => $this->order_id,
            'coupon_code' => $this->coupon_code,
            'qr_code' => url('/').Storage::url($this->qr_path),
            'message' => 'QR code generated successfully.',
            'voucher_id' => $this->voucher->id,
            'voucher_title' => $this->voucher->title,
            'voucher_image' => url('/').Storage::url($this->voucher->image),
            'merchant_name' => $this->voucher->merchant->name,
            'date' => $this->created_at->format('d M Y'),
        ];
    }
}
