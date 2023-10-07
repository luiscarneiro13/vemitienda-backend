<?php

namespace App\Http\Requests\WEB;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
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
        $datos = [
            'name' => 'required|max:120',
        ];

        return $datos;
    }

    public function messages()
    {
        return [
            'name.required'  => 'El campo nombre del Plan es obligatorio',
            'name.max'       => 'El campo nombre del Plan no debe contener mas de 120 caracteres',
        ];
    }
}
