<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MetronomeRequest extends FormRequest
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
            'title' => 'required|string|max:150',
            'artist' => 'nullable|string|max:150',
            // BPM es opcional: hay canciones que solo se listan en una playlist sin metrónomo.
            'bpm' => 'nullable|integer|min:20|max:300',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'El título de la canción es obligatorio',
            'title.max' => 'El título no debe superar los 150 caracteres',
            'artist.max' => 'El artista no debe superar los 150 caracteres',
            'bpm.integer' => 'El BPM debe ser un número entero',
            'bpm.min' => 'El BPM mínimo permitido es 20',
            'bpm.max' => 'El BPM máximo permitido es 300',
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
