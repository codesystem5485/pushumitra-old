<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddanimalProcessRequest extends FormRequest
{
    /**
     * Determine if the pet owner is authorized to make this request.
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
     * @return array
     */
    public function rules()
    {
        $request =  [
            'animal_owner' => 'required',            
            // 'mobile_number' => "required|max:10",
            //'species' => 'required|string',
            //'breed' => 'required|string',
            'sex' => 'required|string',
            'age' => 'required|',
           // 'UID_number' => 'required'
        ];
        if(!$this->id){
            // $request['password'] = 'required';
        }
        return $request;
    }
}
