<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PetownerProcessRequest extends FormRequest
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
            'first_name' => 'required',
            'middle_name' => 'required',
            'last_name' => 'required',
            'email' => "required|unique:users,email,{$this->id}",
            'mobile_number' => "required|max:10|unique:users,mobile_number,{$this->id}",
            'password' => 'required',
            'confirm_password' => 'required',
            'address_1' => 'required|string',
            'address_2' => 'required|string',
            'village' => 'required|string',
            'city_town' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'required|numeric',
        ];
        if(!$this->id){
            // $request['password'] = 'required';
        }
        return $request;
    }
}
