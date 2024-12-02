<?php

namespace App\Http\Requests\Api\Headquarter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewHeadquarterRequest extends FormRequest
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
            'tipologia' => [
                'required',
                Rule::in(["Sede legale", "Sede amministrativa", "Sede operativa", "Magazzino", "Altro"])
            ],
            'indirizzo_via' => ['required'],
            'indirizzo_cap' => ['required'],
            'indirizzo_comune' => ['required'],
            'indirizzo_provincia' => ['required'],
        ];
    }
}
