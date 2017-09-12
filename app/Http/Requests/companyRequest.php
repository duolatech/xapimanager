<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class companyRequest extends FormRequest
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
            'company' => 'required|min:2|max:60',
            'appId' => 'required',
			'appSecret' => 'required|min:6|max:48',
        ];
    }
    
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(){
        return [
            'company.required' => 'A company is required',
            'company.min' => 'A company is least 2 char',
        ];
    }
    
}
