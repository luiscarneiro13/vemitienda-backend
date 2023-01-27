<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;

class ResetRequest extends FormRequest
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
            'password'              => 'required|min:6',
            'password_confirmation' => 'required_with:password|same:password|min:6',
        ];
    }

    public function messages()
    {
        return [
            'password.required'              => 'El campo contraseña es obligatorio',
            'password.min'                   => 'El campo contraseña debe contener al menos 6 caracteres',
            'password_confirmation.required' => 'El campo confirmar contraseña es obligatorio',
            'password_confirmation.same'     => 'Los campos contraseña y confirmar contraseña no coinciden',
            'password_confirmation.min'      => 'El campo confirmar contraseña debe contener al menos 6 caracteres',
        ];
    }
}
