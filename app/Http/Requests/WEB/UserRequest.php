<?php

namespace App\Http\Requests\WEB;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $datos = ['name' => 'required|max:120'];

        if ($this->_method == 'put') {
            $datos['email'] = 'required|' . Rule::unique('users')->ignore($this->user);
        } else {
            $datos['email'] = 'required|email|max:120|unique:users,email';
            $datos['password'] = 'required|min:6';
            $datos['password_confirmation'] = 'required_with:password|same:password|min:6';
        }

        return $datos;
    }

    public function messages()
    {
        return [
            'name.required'                   => 'El campo nombre es obligatorio',
            'name.max'                        => 'El campo nombre no debe contener mas de 120 caracteres',
            'email.required'                  => 'El campo correo es obligatorio',
            'email.email'                     => 'El email ingresado no es valido',
            'email.max'                       => 'El campo correo no debe contener mas de 120 caracteres',
            'email.unique'                    => 'El correo ingresado ya posee una cuenta activa, debe ingresar un correo diferente',
            'password.required'               => 'El campo contraseña es obligatorio',
            'password.min'                    => 'El campo contraseña debe contener al menos 6 caracteres',
            'password_confirmation.required'  => 'El campo confirmar contraseña es obligatorio',
            'password_confirmation.same'      => 'Los campos contraseña y confirmar contraseña no coinciden',
            'password_confirmation.min'       => 'El campo confirmar contraseña debe contener al menos 6 caracteres',
        ];
    }
}
