<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
           'title'=>'required|unique:blogs,title,'.$this->blog->id,
           'short_description'=>'required',
           'feature_image'=>'required',
           'long_description'=>'required',
           'status'=>'required',
           'user_id'=>'integer|required'


        ];
    }
    protected function prepareForValidation()
    {
            $this->merge([
                'user_id'=>auth()->id()
            ]);


    }
}
