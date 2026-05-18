<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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

            'title'=>'required|min:255',
            'short_description'=>'required|min:255',
            'feature_image'=>'required|file',
            'long_description'=>'required',
            'status'=>'required|boolean',
            'user_id'=>'required|integer',

        ];
    }
    protected function prepareForValidation()
    {
            $this->merge(['user_id'=>auth()->id()]);
    }
}
