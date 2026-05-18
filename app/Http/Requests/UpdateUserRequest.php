<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email,'.$this->user->id,
            // 'password'=>['nullable','sometimes','required',Password::min(8)->letters()
            // ->mixedCase()
            // ->numbers()
            // ->symbols()
            // ->uncompromised()],
            'password' => 'nullable|required|string',
            'role'=>'required|string',
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

            if ($this->password != null) {

                $pass = Hash::make($this->password);
                if ($pass ==$this->user->password) {
                    $pass = $this->user->password;
                }
            }

            else {
                $pass = $this->user->password;
            }

            if($this->user->role == 'admin'){
                $this->merge(['role'=>'admin']);
            }

            $this->merge(['password'=>$pass]);


    }
    // public function failedValidation(ValidationValidator $validator)
    // {
    //        dd($validator->errors());
    // }
}
