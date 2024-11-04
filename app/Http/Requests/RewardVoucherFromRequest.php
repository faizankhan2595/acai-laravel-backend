<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RewardVoucherFromRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id'     => 'required',
            'title'       => 'required',
            'price'       => 'required',
            'expiring_on' => 'required',
            'image'       => 'sometimes|mimes:jpeg,jpg,png|max:10000',
            'notes'       => '',
            'terms'       => '',
            'is_featured' => '',
            'old_image' => 'sometimes',
            'voucher_type' => 'required',
            'valid_for' => 'required',
            'discount_subtitle' => '',
            'discount_title' => '',
            'status'      => 'required',
        ];
    }
}
