<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PanjarpolProcessRequest extends FormRequest
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
            'panjarpol_name' => 'required',
			'manager_name' => 'required',
            'mobile_number' => 'required|numeric|digits:10',
            'address' => 'required|string',
            'city_town' => 'required|string',
			'email_id' => 'nullable|email',
            'state' => 'required|string',
            'pincode' => 'required|numeric|digits:6',
			'taluka' => 'nullable|string',
			'district' => 'nullable|string',
        ];
        if(!$this->id){
            // $request['password'] = 'required';
        }
        return $request;
    }
}
