<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'email' => 'required|email|minx:6|max:120|exists:users,email',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'El campo correo es obligatorio',
            'email.email'    => 'El correo ingresado no es valido',
            'email.min'      => 'El campo correo debe tener al menos 6 caracteres',
            'email.max'      => 'El campo correo no debe contener mas de 120 caracteres',
            'email.exists'   => 'Correo inválido',
        ];
    }
}
