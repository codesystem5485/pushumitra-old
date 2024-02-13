<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductsaleProcessRequest extends FormRequest
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
            'product_name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'address' => 'required',
			'city_town' => 'required|string',
			'state' => 'required|string',
            'pincode' => 'required|numeric',
			'email_id' => 'nullable|email',
            'contact_number_of_owner' => 'required|numeric|min:10',
            'contact_name_of_owner' => 'required',
        ];
        if(!$this->id){

        }
        return $request;
    }
}
