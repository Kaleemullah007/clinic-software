<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
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
            'data.*.key_name'=>'required|string',
            'data.*.key_value'=>'required|string',
            'data.*.status'=>'required|boolean',
            'ids.*'=>'sometimes|array',
            'all_ids.*'=>'sometimes|array',
        ];
    }
    protected function prepareForValidation()
    {

        // dd($this->all());
        // $this->merge([
        //     'ids'=>$this->all()['ids'],
        // ]);
            // dd($this->request);
        $data = $this->all()['data'];
        foreach($data as $k => $day){

            // $data[$k]['user_id'] = auth()->id();
            if(!isset($day['status'])){
                $data[$k]['status'] = false;
                continue;
            }
            if($data[$k]['status'] == 'on')
            $data[$k]['status'] = true;
            else
            $data[$k]['status'] = false;


        }

        $this->replace([
            'data'=>$data,
        ]);
        // $this->replace([
        //     'ids'=>$this->ids,
        // ]);
        // $this->replace([
        //     'data'=>$data,
        // ]);


    }
}
