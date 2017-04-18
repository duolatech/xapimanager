<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class debugApiRequest extends FormRequest
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
            'type' => 'required|min:3|max:6',
            'apiurl' => array('regex:/^(http|https){1}(:\/\/)?([\da-z-\.]+)\.([a-z]{2,6})([\/\w \.-?&%-=]*)*\/?$/i'),
        ];
    }
	/**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
     public function messages(){
        return [
            'type.required' => 'A type is required',
        ];
    }
}
