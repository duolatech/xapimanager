<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class classifyRequest extends FormRequest
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
            'classify' => 'required|min:2',
			'description' => 'required|min:5',
			'csrf_user' => 'required|min:2',
        ];
    }
	/**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
     public function messages(){
        return [
            'classify.required' => 'A classify is required',
        ];
    }
}
