<?php

namespace App\Http\Requests\asset;

use Illuminate\Foundation\Http\FormRequest;

class UploadDeliveryRecordRequest extends FormRequest
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
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
            'is_assignment' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'El archivo es obligatorio.',
            'file.file' => 'El archivo debe ser un archivo válido.',
            'file.mimes' => 'El archivo debe ser un archivo de tipo: pdf, jpg, jpeg, png.',
            'file.max' => 'El tamaño máximo del archivo es de 5MB.',
            'is_assignment.required' => 'El campo de asignación es obligatorio.',
            'is_assignment.boolean' => 'El campo de asignación debe ser verdadero o falso.',
        ];
    }
}
