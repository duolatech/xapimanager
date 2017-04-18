<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class apiRequest extends FormRequest
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
            'apiname' => 'required|min:2|max:20',
            'version' => 'required|min:2|max:4',
            'subClassify'=> 'required',
            'gateway' => array('regex:/^(http|https){1}(:\/\/)?([\da-z-\.]+)\.([a-z]{2,6})([\/\w \.-?&%-=\{\}]*)*\/?$/i'),
            'local' => array('regex:/^(http|https){1}(:\/\/)?([\da-z-\.]+)\.([a-z]{2,6})([\/\w \.-?&%-=\{\}]*)*\/?$/i'),
        ];
    }
	/**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
     public function messages(){
        return [
            'apiname.required' => 'A apiname is required',
        ];
    }
}
