<?php

namespace App\Http\Requests\DevelopmentRequest;

use Illuminate\Foundation\Http\FormRequest;

class RegisterProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'percentage' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'percentage.required' => 'El porcentaje es requerido.',
            'percentage.integer' => 'El porcentaje debe ser un número entero.',
            'percentage.min' => 'El porcentaje debe ser al menos 0.',
            'percentage.max' => 'El porcentaje no puede ser mayor a 100.',
            'notes.max' => 'Las notas no pueden exceder los 1000 caracteres.',
        ];
    }
}
