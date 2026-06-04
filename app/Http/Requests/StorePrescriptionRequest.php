<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
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
            'type'           => 'nullable|in:prescription,note',
            'remarks'        => 'nullable|string',
            'medicine'       => 'required_if:type,prescription|nullable|string',
            'dosage'         => 'nullable|string',
            'appointment_id' => 'required|exists:appointments,id',
            'user_id'        => 'required|exists:users,id',
            'doctor_id'      => 'required|exists:users,id',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'doctor_id' => auth()->id(),
            'type'      => $this->type ?? 'prescription',
        ]);
    }
}
