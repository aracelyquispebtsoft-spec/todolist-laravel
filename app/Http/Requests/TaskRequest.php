<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'is_completed' => ['required', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
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
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede superar los :max caracteres.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'tags.*.exists' => 'Alguna de las etiquetas seleccionadas no existe.',
        ];
    }
}
