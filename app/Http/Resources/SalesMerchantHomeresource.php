<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesMerchantHomeresource extends JsonResource
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
            'ref_id' => $this->reference_id,
            'amount_paid' => config('admin.default_currency').$this->amount,
            'points_earned' => $this->points,
            'scanned_on' => $this->scanned_on->format('d M Y g:i A'),
            'points_added_by' => $this->generatedBy->name,
            'scanned_by' => new UserResource($this->scannedBy),
        ];
    }
}
