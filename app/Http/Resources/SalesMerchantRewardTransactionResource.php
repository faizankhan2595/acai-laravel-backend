<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SalesMerchantRewardTransactionResource extends JsonResource
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
            'order_id'         => $this->order_id,
            'title'         => $this->voucher->title,
            'image'         => (!is_null($this->voucher->image)) ? url('/').Storage::url($this->voucher->image) : url('/').'/img/voucher-img.png',
            'merchant'      => $this->voucher->merchant->name,
            'customer_name' => $this->user->name,
            'amount'        => $this->amount,
            'membership'    => $this->user->membership(),
            'balance'       => $this->user->balance(),
            'redeemed_on'   => $this->redeemed_on->format('d M Y'),
        ];
    }
}
