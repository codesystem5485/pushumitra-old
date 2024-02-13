<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransporterProcessRequest extends FormRequest
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
            'transporter_name' => 'required',
            'vehicle_name' => 'required',
            'mobile_number' => "required|numeric|digits:10",
            'address' => 'required',
            'city_town' => 'required',
			'email_id' => 'nullable|email',
            'state' => 'required',
            'pincode' => 'required|numeric|digits:6',
        ];
        if(!$this->id){
            // $request['password'] = 'required';
        }
        return $request;
    }
}
