<?php

namespace App\Http\Requests\WEB;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'name'    => 'required|min:3|max:120',
            'email'   => 'required|min:3|email|max:120',
            'phone'   => 'required|min:3|max:120',
            'message' => 'required|min:3|max:120',
            'g-recaptcha-response' => 'required|captcha',
        ];

        return $datos;
    }

    public function messages()
    {
        return [
            'name.required'    => 'El nombre es obligatorio',
            'name.max'         => 'El nombre no debe contener mas de 120 caracteres',
            'name.min'         => 'El nombre debe contener al menos 3 caracteres',
            'email.required'   => 'Escriba un email',
            'email.email'      => 'Email inválido',
            'email.max'        => 'El email no debe contener mas de 120 caracteres',
            'email.min'        => 'El email debe contener al menos 3 caracteres',
            'phone.required'   => 'El teléfono es obligatorio',
            'phone.max'        => 'El teléfono no debe contener mas de 120 caracteres',
            'phone.min'        => 'El teléfono debe contener al menos 3 caracteres',
            'message.required' => 'Escriba un mensaje',
            'message.max'      => 'El mensaje no debe contener mas de 4000 caracteres',
            'message.min'      => 'El mensaje debe contener al menos 3 caracteres',
        ];
    }
}
