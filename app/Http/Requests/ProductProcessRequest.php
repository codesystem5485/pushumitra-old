<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductProcessRequest extends FormRequest
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
            'product_owner' => 'required',
            'mobile_number' => 'required',
        ];
        if(!$this->id){

        }
        return $request;
    }
}
