<?php

namespace App\Http\Requests\DevelopmentRequest;

use App\Enums\DevelopmentRequest\DevelopmentRequestPriority;
use App\Enums\DevelopmentRequest\DevelopmentRequestStatus;
use Illuminate\Foundation\Http\FormRequest;

class StoreDevelopmentRequest extends FormRequest
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
            'priority' => 'required|in:' . DevelopmentRequestPriority::implode(),
            'description' => 'required|string',
            'impact' => 'nullable|string',
            'estimated_hours' => 'nullable|integer|min:0|digits_between:1,6',
            'estimated_end_date' => 'nullable|date|after_or_equal:today',
            'area_id' => 'required|exists:area,id_area',
            'requirement_file' => 'nullable|file|max:4096|mimes:pdf',
            
            // 'requested_by_id' => 'required|exists:ost_staff,staff_id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 255 caracteres.',
            'priority.required' => 'La prioridad es obligatoria.',
            'priority.in' => 'La prioridad seleccionada no es válida.',
            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'impact.string' => 'El impacto debe ser una cadena de texto.',
            'estimated_hours.integer' => 'Las horas estimadas deben ser un número entero.',
            'estimated_hours.min' => 'Las horas estimadas no pueden ser negativas.',
            'estimated_hours.digits_between' => 'Las horas estimadas no pueden tener más de 6 dígitos.',
            'estimated_end_date.date' => 'La fecha de finalización estimada no es una fecha válida.',
            'estimated_end_date.after_or_equal' => 'La fecha de finalización estimada debe ser hoy o en el futuro.',
            'area_id.required' => 'El área solicitada es obligatoria.',
            'area_id.exists' => 'El área solicitada no existe.',
            'requirement_file.file' => 'El archivo debe ser un archivo válido.',
            'requirement_file.max' => 'El archivo no debe exceder los 4 MB.',
            'requirement_file.mimes' => 'El archivo debe ser un archivo PDF.',
            // 'requested_by_id.required' => 'El solicitante es obligatorio.',
            // 'requested_by_id.exists' => 'El solicitante no existe.',
        ];
    }
}
