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
            'first_name' => 'required',
            'middle_name' => 'required',
            'last_name' => 'required',
            'email' => "required|unique:users,email,{$this->id}",
            'mobile_number' => "required|max:10|unique:users,mobile_number,{$this->id}",
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
            'pm_collage_name' => 'required|string',
            'pm_collage_address' => 'required|string',            
            'pm_nominee_name'=>'required|String',
            'pm_nominee_dob'	=>'required|date',
            'pm_collage_address'	=>'required|String',
            'pm_nominee_relationship'	=>'required|String',
            'pm_aadhar_no'	=>'required|numeric',
            'pm_aadhar_photo_front'	=>'required|max:10240',
            'pm_aadhar_photo_back'	=>'required|max:10240',
            'job_type'	=>'required',
            'pm_pan_no'	=>'required|',
            'pm_pan_photo'	=>'required|max:10240',
            'pm_bank_name'	=>'required|String',
            'pm_account_no'	=>'required|numeric',
            'pm_ifsc_code'	=>'required',
            'pm_cheque_photo'	=>'required|max:10240',
            'pm_name_of_org'	=>'required|String',
        ];
        if(!$this->id){
            $request['password'] = 'required';
            $request['confirm_password'] = 'required';
        }
        return $request;
    }
}
