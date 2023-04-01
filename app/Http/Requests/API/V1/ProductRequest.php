<?php

namespace App\Http\Requests\API\V1;

use App\Models\Company;
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


    protected function prepareForValidation()
    {
        // $this->merge([
        //     'price' => floatval(str_replace(',', '', $this->price))
        // ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = Auth::user();

        // Busco la company y veo si es tienda o catálogo
        $company = Company::where('user_id', $user->id)->first();
        if ($company->is_shop) {
            //Como es tienda, se requiere la cantidad disponible y el precio
            $datos = [
                'name'         => [
                    'required', 'min:3', 'max:120',
                    Rule::unique('products')->where('user_id', $user->id)->where('category_id', $this->category_id)->where('name', $this->name)->ignore($this->product)
                ],
                'category_id' => 'required|integer|exists:categories,id',
                'price'       => 'required|between:0,999999999999999999.99',
                'available'   => 'integer|min:0|max:100000',
                'share'       => 'integer|min:0|max:1'
            ];
        } else {
            //Como solo es un catálogo no se requiere cantidad disponible ni precio
            $datos = [
                'name'         => [
                    'required', 'min:3', 'max:120',
                    Rule::unique('products')->where('user_id', $user->id)->where('category_id', $this->category_id)->where('name', $this->name)->ignore($this->product)
                ],
                'category_id' => 'required|integer|exists:categories,id',
                'price'       => 'between:0,999999999999999999.99',
                'share'       => 'integer|min:0|max:1'
            ];
        }

        return $datos;
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
            'price.min'            => 'El precio mínimo es 0 y máximo 999999999999999999',
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
