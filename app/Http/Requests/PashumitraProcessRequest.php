<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PashumitraProcessRequest extends FormRequest
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
            //'pm_collage_name' => 'required|string',
            //'pm_collage_address' => 'required|string',            
            'pm_nominee_name'=>'nullable|String',
            'pm_nominee_dob'	=>'nullable|date',
			'pm_nominee_relationship'	=>'nullable|String',
            'pm_aadhar_no'	=>'nullable|numeric',
            'job_type'	=>'required',
            'pm_pan_no'	=>'nullable',
            'pm_bank_name'	=>'nullable|String',
            'pm_account_no'	=>'nullable|String',
            'pm_cheque_photo'=>'max:15000',
			'profile_photo'=>'mimes:jpeg,jpg,png|max:15000',
            
        ];
        if(!$this->id){
            $request['password'] = 'required';
            $request['confirm_password'] = 'required';
        }
        return $request;
    }
}
