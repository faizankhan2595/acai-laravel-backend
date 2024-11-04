<?php

namespace App\Http\Requests;

use App\Blog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogStoreRequest extends FormRequest
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
            'category_id' => 'required',
            'title' => ['required', Rule::unique('blogs', 'title')->ignore($this->blog)],
            'post_body' => 'required',
            'is_featured' => '',
            'allow_comments' => '',
            'status' => 'required',
            'old_image' => 'sometimes',
            'featured_video' => 'sometimes',
            'tags' => 'sometimes',
            'images.*' => 'sometimes|image|mimes:jpeg,jpg,png|max:10000',

        ];
    }
}
