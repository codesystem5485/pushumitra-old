<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChemistProcessRequest extends FormRequest
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
            'shop_name' => 'required',
            'owner_name' => 'required|string',
            'mobile_number' => "required|numeric|digits:10",
            'address_line_1' => 'required|string',
            'city_town' => 'required|string',
			'email_id' => 'nullable|email',
            'state' => 'required|string',
            'pincode' => 'required|numeric',
			'taluka' => 'nullable|string',
			'district' => 'nullable|string',
        ];
        if(!$this->id){
            // $request['password'] = 'required';
        }
        return $request;
    }
}
