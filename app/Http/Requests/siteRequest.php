<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class siteRequest extends FormRequest
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
            'title' => 'required|min:2',
            'sitename' => 'required',
        ];
    }
    
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(){
        return [
            'title.required' => 'A title is required',
            'title.min' => 'A title is least 2 char',
            'sitename.required'  => 'A message is required',
        ];
    }
    
}
