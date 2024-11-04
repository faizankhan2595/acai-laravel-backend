<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $initial = substr(ucfirst($this->name), 0, 1);
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->when(($request->user()->getRoleNames()->first() == 'user' || $request->user()->id === $this->id), function () {
                return $this->email;
            }),
            'dob'           => (!is_null($this->dob)) ? $this->dob->format('d M Y') : null,
            'mobile_number' => $this->when((in_array($request->user()->getRoleNames()->first(),['user','sales_person']) || $request->user()->id === $this->id), function () {
                return $this->mobile_number;
            }),
            'avatar'        => ($this->avatar) ? url('/') . Storage::url($this->avatar) : url('/') . Storage::url('/user/default/' . $initial . '.png'),
            'role'          => $this->getRoleNames()->first(),
            'membership'    => $this->membership(),
            'gold_exp_date' => !is_null($this->gold_expiring_date) ? Carbon::parse($this->gold_expiring_date)->format('d M Y') : null,
            'balance'       => $this->balance(),
            'member_since'  => $this->created_at->format('d M Y'),
        ];
    }
}
