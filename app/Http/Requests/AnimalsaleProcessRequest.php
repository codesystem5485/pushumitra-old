<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnimalsaleProcessRequest extends FormRequest
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
            'UID_number' => 'required',
            'species' => 'required',
            'breed' => "required",
            'age' => 'required|numeric',
            'sex' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'required',
            'address' => 'required',
            'contact_number_of_owner' => 'required|numeric|min:10',
            'contact_name_of_owner' => 'required',
        ];
        if(!$this->id){
            // $request['password'] = 'required';
        }
        return $request;
    }
}
