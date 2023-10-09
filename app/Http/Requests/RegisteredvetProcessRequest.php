<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisteredvetProcessRequest extends FormRequest
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
            'alternate_mobile_number' => "required|max:10",
            'address_line_1' => 'required|string',
            'address_line_2' => 'required|string',
            'village' => 'required|string',
            'city_town' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'required|numeric',      
            'age' => 'required',                
            'nationality' => 'required',
            'sex' => 'required',
            'date_of_birth' => 'required|date|before:today',
            'marital_status' => 'required',      
            'education'=> 'required',
            'education_certificate'=> 'required|max:10240',
            'job_type'	=>'required',
            'rv_state_verternity_council'=>'required|string',
            'rv_state_verternity_council_no'=>'required|numeric',
            'rv_speciality'=>'required|string',
            'rv_name_of_working_org'=>'required|string',
            'rv_working_state'=>'required|string',
            'rv_working_city_town'=>'required|string',
            'rv_working_village'=>'required|string',
            'rv_working_pincode'=>'required|numeric',
            
        ];
        if(!$this->id){
            $request['password'] = 'required';
            $request['confirm_password'] = 'required';
        }
        return $request;
    }
}
