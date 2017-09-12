<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class menuRequest extends FormRequest
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
            'path' => 'required|min:1',
            'title' => 'required|min:2',
        ];
    }
    
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(){
        return [
            'path.required' => 'A title is required',
            'path.min' => 'A title is least 1 char',
            'title.required'  => 'A message is required',
			'title.min' => 'A title is least 2 char',
        ];
    }
    
}
