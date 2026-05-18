<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClinicRequest extends FormRequest
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
            'name'               => 'required|max:255|unique:clinics,name',
            'phone'              => 'required',
            'support_email'      => 'required|email',
            'notification_email' => 'required|email',
            'address'            => 'required',
            'status'             => 'boolean',
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
    }
}
