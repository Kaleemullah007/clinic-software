<?php

namespace App\Http\Requests;
use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
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
            'meta_tag'=>'required|string',
            'meta_description'=>'required|string',
            'keywords'=>'required|string',
            'heading'=>'required|string',
            'category_id'=>'required|integer',
            'description'=>'required|string',
            'procedure_heading'=>'required|string',
            'procedure_description'=>'required|string',
            'title'=>'required|string',
            'is_discounted'=>'sometimes|boolean',
            'price'=>'required|numeric',
            'discounted_price'=>'required|numeric',
            'is_button_availalble'=>'sometimes|boolean',
            'status'=>'sometimes|boolean',
            'url'=>'required|url',
            'slug'=>'required|string|unique:pages,slug,'.$this->page->id,
        ];
    }

    protected function prepareForValidation()
    {

        $this->status = $this->status??false;
        if($this->status == 'on')
            $this->status = true;

        $this->is_discounted = $this->is_discounted??false;

        if($this->is_discounted == 'on')
            $this->is_discounted = true;


        $this->is_button_availalble=$this->is_discounted ??false;

        if($this->is_button_availalble == 'on')
            $this->is_button_availalble = true;
            if(!empty($this->slug))
            $this->slug = Str::slug($this->slug);
            else
            $this->slug = Str::slug($this->title);

            $this->merge([
                'status'=>$this->status,
                'is_discounted'=>$this->is_discounted,
                'is_button_availalble'=>$this->is_button_availalble,
                'slug'=>$this->slug
            ]);
            // dd($this->all());
        // $data = $this->all()['data'];
        // foreach($data as $k => $day){

        //     $data[$k]['user_id'] = auth()->id();
        //     if(!isset($day['status'])){
        //         $data[$k]['status'] = false;
        //         continue;
        //     }
        //     if($data[$k]['status'] == 'on')
        //     $data[$k]['status'] = true;
        //     else
        //     $data[$k]['status'] = false;


        // }

        // $this->replace([
        //     'data'=>$data,
        // ]);
        // $this->replace([
        //     'ids'=>$this->ids,
        // ]);
        // $this->replace([
        //     'data'=>$data,
        // ]);


    }
}
