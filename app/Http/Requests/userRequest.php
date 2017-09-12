<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class userRequest extends FormRequest
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
        return [
            'username' => 'required|min:2|max:20',
            'phone' => array('regex:/^1\d{10}$/i'),
            'email' => 'required|email',
            'password' => 'required|min:6|max:20'
        ];
    }
	/**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
     public function messages(){
        return [
            'username.required' => 'A username is required',
        ];
    }
}
