<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BreederProcessRequest extends FormRequest
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
				'breeder_name' => 'required',
				//'firm_registration_number' => 'required',
				//'mobile_number' => "required|numeric|digits:10",
				'animal_breed' => 'required',
				'animal_description' => 'required',
				'age' => 'required|numeric',
				'vaccination_done' => 'required',
				'expected_price' => 'required',
				'address' => 'required',
				'state' => 'required',
				'city_town'=>'required',
				'taluka'=>'nullable|string',
				'district'=>'nullable|string',
				'pincode'=>'required|numeric|digits:6',
        ];
        if(!$this->id){
            // $request['password'] = 'required';
        }
        return $request;
    }
}
