<?php

namespace App\Http\Requests\AdminControl;

use App\Enums\InfrastructureEvents\InfrastructureEventsType;
use Illuminate\Foundation\Http\FormRequest;

class StoreInfrastructureEventRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:' . InfrastructureEventsType::implodeValues(),
            'date' => 'required|date',
            // 'responsible_id' => 'required|integer|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no puede exceder los 255 caracteres.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'type.required' => 'El tipo de evento es obligatorio.',
            'type.in' => 'El tipo de evento seleccionado no es válido.',
            'date.required' => 'La fecha del evento es obligatoria.',
            'date.date' => 'La fecha del evento no es válida.',
            // 'responsible_id.required' => 'El responsable del evento es obligatorio.',
            // 'responsible_id.integer' => 'El ID del responsable debe ser un número entero.',
            // 'responsible_id.exists' => 'El responsable seleccionado no existe.',
        ];
    }
}
