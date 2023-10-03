<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserProcessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
        $reqest =  [
            'name' => 'required',
            'email' => "required|unique:users,email,{$this->id}",
            'phone_number' => "required|max:10|unique:users,phone_number,{$this->id}",
        ];
        if(!$this->id){
            // $reqest['password'] = 'required';
        }
        return $reqest;
    }
}
