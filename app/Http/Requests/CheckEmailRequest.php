<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckEmailRequest extends FormRequest
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
            'name'=>'required|string',
            'email' => 'required|email|unique:users,email,'.auth()->id(),
            'avatar' => 'nullable|sometimes|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'password' => 'nullable|required_with:NewPassword|string|confirmed',
             'password' => 'nullable|sometimes|required_with:password|confirmed',

        ];
    }
}
