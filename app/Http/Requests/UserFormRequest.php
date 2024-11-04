<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserFormRequest extends FormRequest
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
        $modelkey = isset($this->route()->parameterNames()[0]) ? $this->route()->parameterNames()[0] : 'user';
        return [
            'name'                 => 'required',
            'email'                => ['required', 'email', Rule::unique('users', 'email')->ignore($this->{$modelkey})],
            'mobile_number'        => ['required', Rule::unique('users', 'mobile_number')->ignore($this->{$modelkey})],
            'dob'                  => '',
            'gender'               => 'required',
            'avatar'               => 'sometimes|mimes:jpeg,jpg,png|max:10000',
            'old_avatar'           => 'sometimes',
            'password'             => 'sometimes|confirmed|min:6',
            'role'                 => 'required',
            'is_featured'          => 'sometimes',
            'is_project_acai'      => 'sometimes',
            'account_status'       => 'required',
            'sort_order'           => '',
            'membership_type'      => '',
            'gold_activation_date' => '',
            'gold_expiring_date'   => '',
        ];
    }
}
