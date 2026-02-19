<?php

namespace App\Http\Requests\asset;

use App\Enums\Asset\AssetStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
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
        ds("llegue al request", $this->all());
        return [
            'status' => 'required|string|in:'. AssetStatus::implode(),
            'description' => 'required_if:status,'.AssetStatus::IN_REPAIR->value.'|string|min:3|max:400',
            'date' => 'required_if:status,'.AssetStatus::IN_REPAIR->value.'|date',
            'images.*' => 'required_if:status,'.AssetStatus::IN_REPAIR->value.'|image|mimes:jpeg,png,jpg,gif|max:2048',
        
        ];
    }

        public function messages()
        {
            return [
                'status.required' => 'El campo estado es obligatorio.',
                'status.in' => 'El estado seleccionado no es válido.',
                'description.required_if' => 'La descripción es obligatoria cuando el estado es "En Reparación".',
                'description.string' => 'La descripción debe ser una cadena de texto.',
                'description.min' => 'La descripción debe tener al menos :min caracteres.',
                'description.max' => 'La descripción no puede tener más de :max caracteres.',
                'date.required_if' => 'La fecha es obligatoria cuando el estado es "En Reparación".',
                'date.date' => 'La fecha no tiene un formato válido.',
                'images.*.required_if' => 'Las imágenes son obligatorias cuando el estado es "En Reparación".',
                'images.*.image' => 'Cada archivo debe ser una imagen.',
                'images.*.mimes' => 'Cada imagen debe ser un archivo de tipo: jpeg, png, jpg, gif.',
                'images.*.max' => 'Cada imagen no puede superar los 2MB de tamaño.',
            ];
        }

}
