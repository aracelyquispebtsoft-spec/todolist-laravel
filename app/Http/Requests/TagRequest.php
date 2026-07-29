<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * El proyecto no maneja autenticación, así que toda petición está permitida.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Estas reglas son además la lista blanca del proyecto: validated() devuelve
     * únicamente los campos declarados aquí.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                // Al editar hay que excluir la propia etiqueta, o chocaría
                // consigo misma en el índice único.
                Rule::unique('tags', 'name')->ignore($this->route('tag')),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede superar los :max caracteres.',
            'name.unique' => 'Ya existe una etiqueta con ese nombre.',
        ];
    }
}
