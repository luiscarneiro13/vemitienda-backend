<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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

        return [
            'name'         => [
                'required', 'min:3', 'max:120',
                Rule::unique('products')->where('user_id', $user->id)->where('category_id', $this->category_id)->where('name', $this->name)->ignore($this->product)
            ],
            'category_id' => 'required|integer|exists:categories,id',
            'description' => 'required|min:3|max:4000',
            'price'       => 'numeric|min:0|max:1000000000000',
            'share'       => 'integer|min:0|max:1'
        ];
    }

    public function messages()
    {
        return [
            'name.required'        => 'El nombre del producto es obligatorio',
            'name.min'             => 'El nombre del producto debe contener al menos 3 caracteres',
            'name.max'             => 'El nombre del producto no debe contener mas de 120 caracteres',
            'name.unique'          => 'Ya posee un producto con el mismo nombre y la misma categoría',
            'category_id.required' => 'La categoría es obligatoria',
            'category_id.integer'  => 'Debe seleccionar una categoría',
            'category_id.exists'   => 'La categoría no existe',
            'description.required' => 'La descripción es obligatoria',
            'description.min'      => 'La descripción debe contener al menos 3 caracteres',
            'description.max'      => 'La descripción no debe contener mas de 4000 caracteres',
            'price.numeric'        => 'El precio debe ser un número decimal',
            'price.min'            => 'El precio mínimo es 0',
            'price.max'            => 'El precio máximo es 1.000.000.000.000',
            'share.integer'        => 'El campo compartir debe ser un número entero',
            'share.min'            => 'El campo compartir mínimo es 0',
            'share.max'            => 'El campo compartir máximo es 1',
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
