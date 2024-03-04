<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EasycaresProcessRequest extends FormRequest
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
            'title' => 'required',
			'user_code' => 'required',
            'solutions' => "required",
            
        ];
        if(!$this->id){
            // $request['password'] = 'required';
        }
        return $request;
    }
}
