<?php

namespace App\Http\Requests\API\V1;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
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
            'email'    => 'required|email|max:120|exists:users,email',
            'password' => 'required|min:6',
        ];
    }

    public function messages()
    {
        return [
            'email.required'                 => 'El campo correo es obligatorio',
            'email.email'                    => 'El email ingresado no es valido',
            'email.max'                      => 'El campo correo no debe contener mas de 120 caracteres',
            'email.exists'                   => 'Datos inválidos',
            'password.required'              => 'El campo contraseña es obligatorio',
            'password.min'                   => 'El campo contraseña debe contener al menos 6 caracteres',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $validator->errors()->add('message', 'Datos erróneos');
        $data['errors'] = $validator->errors();
        $data['status'] = 400;
        throw new HttpResponseException(response()->json($data, 200));
    }
}
