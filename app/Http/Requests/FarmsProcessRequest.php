<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FarmsProcessRequest extends FormRequest
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
            'farm_name' => 'required',
			'incharge_name' => 'required',
            'mobile_number' => 'required|numeric|digits:10',
			'sub_category'=>'required',
            'address' => 'required|string',
            'city_town' => 'required|string',
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
