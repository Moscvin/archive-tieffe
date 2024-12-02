<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialRequest extends FormRequest
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
            'description' => 'required',
            'quantity' => 'required|integer',
            'work_id' => 'required',
            'code' => 'nullable|max:10'
        ];
    }

    public function messages()
    {
        return [
            'description.required' => 'Devi inserire descruzione.',
            'quantity.required' => 'Devi inserire il quantita.',
            'work_id.required' => 'Devi inserire il lavoro id.',
            'code.max' => 'Devi inserire un codice più breve.'
        ];
    }
}
