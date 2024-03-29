<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
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
        $user = Auth::user();
        $userId = $user ? $user->id : null;

        return [
            'name' => [
                'required', 'min:3', 'max:120',
                Rule::unique('companies')->ignore($userId),
            ],
            'slogan' => ['required', 'min:3', 'max:120'],
            'phone'  => 'required|min:3|max:120'
        ];
    }

    public function messages()
    {
        return [
            'name.required'   => 'El nombre de la empresa es obligatorio',
            'name.min'        => 'El nombre de la empresa debe contener al menos 3 caracteres',
            'name.max'        => 'El nombre de la empresa no debe contener mas de 120 caracteres',
            'name.unique'     => 'No se puede agregar este nombre de empresa',
            'slogan.required' => 'El slogan de la empresa es obligatorio',
            'slogan.min'      => 'El slogan de la empresa debe contener al menos 3 caracteres',
            'slogan.max'      => 'El slogan de la empresa no debe contener mas de 120 caracteres',
            'phone.required'  => 'El número de teléfono de la empresa es obligatorio',
            'phone.min'       => 'El número de teléfono de la empresa debe contener al menos 3 caracteres',
            'phone.max'       => 'El número de teléfono de la empresa no debe contener mas de 120 caracteres',
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
