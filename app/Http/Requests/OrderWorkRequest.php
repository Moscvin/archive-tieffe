<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderWorkRequest extends FormRequest
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

    public function rules()
    {
        return [
            'technician' => 'required',
            'date' => 'required',
            'hours' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'technician.required' => 'Si prega di selezionare il nome del tecnico.',
            'date.required' => 'Si prega di selezionare il data di lavore.',
            'hours.required' => 'Si prega di selezionare il numero di ore lavorate.',
        ];
    }
}


