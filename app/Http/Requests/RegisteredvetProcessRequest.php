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
            'education'=> 'required',
            'education_certificate'=> 'nullable|max:15000',
            'pm_aadhar_no'	=>'nullable|numeric',
            'job_type'	=>'required',
            'pm_pan_no'	=>'nullable',
            'profile_photo'=>'mimes:jpeg,jpg,png|max:15000',
			'rv_state_verternity_council_no'=>'required|numeric',
            'rv_speciality'=>'required|string',
            'rv_name_of_working_org'=>'required|string',
           
            
        ];
        if(!$this->id){
            $request['password'] = 'required|min:6';
            $request['confirm_password'] = 'required|min:6';
        }
        return $request;
    }
}
