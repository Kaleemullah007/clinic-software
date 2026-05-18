<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessHourRequest extends FormRequest
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
            'data'=>'required|array',
            "data.*.day"=>["required"],
            "data.*.from"=>["required","date_format:H:i:s"],
            "data.*.to"=>["required","date_format:H:i:s"],
            "data.*.step"=>["required",'integer',"min:1"],
            "data.*.user_id"=>["required",'integer'],
            "data.*.is_day"=>["boolean"],
        ];
    }
    protected function prepareForValidation()
    {
        $data = $this->all()['data'];
        foreach($data as $k => $day){

            $data[$k]['user_id'] = auth()->id();
            $data[$k]['clinic_id'] = 1;  // will add Functionality
            if(!isset($day['is_day'])){
                $data[$k]['is_day'] = false;
                continue;
            }
            if($data[$k]['is_day'] == 'on')
            $data[$k]['is_day'] = true;
            else
            $data[$k]['is_day'] = false;
        }
        $this->replace([
            'data'=>$data
        ]);


    }
}
