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
            'first_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,{$this->id}",
            'mobile_number' => "required|numeric|unique:users,mobile_number,{$this->id}",
            //'password' => 'required',
            //'confirm_password' => 'required',
            'address_line_1' => 'required|string',
            'address_line_2' => 'required|string',
            'village' => 'required|string',
            'city_town' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'required|numeric',
        ];
        if(!$this->id){
            $request['password'] = 'required';
            $request['confirm_password'] = 'required';
        }
        return $request;
    }
}
