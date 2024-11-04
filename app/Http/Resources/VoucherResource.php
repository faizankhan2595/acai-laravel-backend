<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class VoucherResource extends JsonResource
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
            'title' => $this->title,
            'discount_title' => $this->discount_title,
            'discount_subtitle' => $this->discount_subtitle,
            'price' => $this->price,
            'image' => (!is_null($this->image)) ? url('/').Storage::url($this->image) : url('/').'/img/voucher-img.png',
            'notes' => $this->notes,
            'terms' => $this->terms,
            'is_acai_voucher' => $this->merchant->is_project_acai,
            'merchant_name' => $this->merchant->name,
            'merchant_center' => $this->merchant->center,
            'voucher_type' => $this->voucher_type,
            'expiry_date' => $this->expiring_on->format('d M Y'),
            'expiry_date_formated' => $this->expiring_on->format('d/m/Y'),
        ];
    }
}
