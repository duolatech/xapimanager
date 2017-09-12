<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class envRequest extends FormRequest
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
            'envname' => 'required',
            'envid'=> 'required',
            'domain' => array('regex:/^((https|http)?:\/\/)+[A-Za-z0-9]+(\.[A-Za-z0-9]+)+(\/)?$/i'),
        ];
    }
	/**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
     public function messages(){
        return [
            'envname.required' => 'A envname is required',
        ];
    }
}
