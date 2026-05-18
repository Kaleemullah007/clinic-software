<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
class StoreCategoryRequest extends FormRequest
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
            'price'=>'required|decimal:0,2',
            // 'slug'=>'required|string',
            'status'=>'sometimes|boolean',

        ];
    }
    protected function prepareForValidation()
    {
        if ($this->has('status'))
            if($this->status == 'on')
                $this->merge(['status'=>1]);
            else
                $this->merge(['status'=>0]);
        else
            $this->merge(['status'=>0]);
            $this->merge(['slug'=>Str::slug($this->slug)]);


    }
}
