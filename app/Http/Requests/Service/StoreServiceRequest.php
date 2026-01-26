<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'required|url',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del servicio es obligatorio.',
            'name.string' => 'El nombre del servicio debe ser una cadena de texto.',
            'name.max' => 'El nombre del servicio no debe exceder los 255 caracteres.',
            'description.string' => 'La descripción del servicio debe ser una cadena de texto.',
            'url.required' => 'La URL del servicio es obligatoria.',
            'url.url' => 'La URL del servicio debe ser una URL válida.',
            'username.string' => 'El nombre de usuario debe ser una cadena de texto.',
            'username.max' => 'El nombre de usuario no debe exceder los 255 caracteres.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.max' => 'La contraseña no debe exceder los 255 caracteres.',
            'is_active.required' => 'El estado del servicio es obligatorio.',
            'is_active.boolean' => 'El estado del servicio debe ser verdadero o falso.',
        ];
    }
}
