<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnimalownerProcessRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        $request =  [
            'full_name' => 'required',
            'email' => "nullable|unique:users,email,{$this->id}",
            'mobile_number' => "required|max:10|unique:users,mobile_number,{$this->id}",
            'address_line_1' => 'required|string',
            'district' => 'nullable|string',
            'taluka' => 'nullable|string',
            'city_town' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'required|numeric',      
            'sex' => 'required',
            'date_of_birth' => 'nullable|date|before:today',
        ];
        if(!$this->id){
            $request['password'] = 'required|min:6';
            $request['confirm_password'] = 'required|min:6';
        }
        return $request;
    }
}
