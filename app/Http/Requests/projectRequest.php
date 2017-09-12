<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class projectRequest extends FormRequest
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
            'proname' => 'required|min:2|max:20',
            'desc' => 'required|min:6',
        ];
    }
    
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(){
        return [
            'proname.required' => 'A proname is required',
            'proname.min' => 'A proname is least 2 char',
            'desc.required'  => 'A desc is required',
        ];
    }
    
}
