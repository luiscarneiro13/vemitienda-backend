<?php

namespace App\Http\Requests\WEB;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name.required'  => 'El campo nombre de la Categoría es obligatorio',
            'name.max'       => 'El campo nombre de la Categoría no debe contener mas de 120 caracteres',
        ];
    }
}
