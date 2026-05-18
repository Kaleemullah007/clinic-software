<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
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
            // 'service_id'=>'required|array',
            // 'doctor_id'=>'required|integer',
            // 'clinic_id'=>'required|integer',
            // 'name'      => 'required',
            // 'phone'      => 'required|numeric|digits_between:9,11',
            // 'email'     => 'sometimes|email',
            // 'date'   => 'required',
            
            'service_id.*.service_id'=>'required|integer',
            'service_id.*.price'=>'required|decimal:0,2',
            'doctor_id'=>'required|integer',
            'clinic_id'=>'required|integer',
            'id'=>'required|integer',
            'name'      => 'required|max:255',
            'phone'      => 'required|numeric|digits_between:9,11',
            'whatsapp_number'=> 'required|numeric|digits_between:9,11',
            'date'   => 'required',
            'remaining_amount'=>'decimal:0,2',
            'appointment_status'=>'required',
            'gender'=>'required',
            'discount'=>'sometimes|nullable|decimal:0,2',
            'paid_amount'=>'sometimes|nullable|decimal:0,2',
            'email'     => 'sometimes|nullable|email',
            'time'   => 'sometimes|nullable',
            'is_paid'=>'required',
            
            
        ];
    }
}
