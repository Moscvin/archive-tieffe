<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
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
            'tipologia' => 'nullable',
            'indirizzo_sl' => 'nullable',
            'cap_sl' => 'nullable',
            'comune_sl' => 'nullable',
            'provincia_sl' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'tipologia.required' => 'Il campo tipologia è richiesto.',
            'indirizzo_sl.required' => 'Il campo indirizzo è richiesto.',
            'cap_sl.required' => 'Il campo cap è richiesto.',
            'comune_sl.required' => 'Il campo comune è richiesto.',
            'provincia_sl.required' => 'Il campo provincia è richiesto.',
        ];
    }
}
