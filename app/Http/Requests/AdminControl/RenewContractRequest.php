<?php

namespace App\Http\Requests\AdminControl;

use Illuminate\Foundation\Http\FormRequest;

class RenewContractRequest extends FormRequest
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
            // 'contract_id' => 'required|exists:contracts,id',
            'new_end_date' => 'required|date|after:today',
            'notes' => 'nullable|string|max:1000',
            'alert_days_before' => 'required|integer|min:0',
        ];

    }

    public function messages(): array
    {
        return [
            'new_end_date.required' => 'La fecha de fin es obligatoria.',
            'new_end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'new_end_date.after' => 'La fecha de fin debe ser una fecha futura.',
            'notes.string' => 'Las notas deben ser una cadena de texto.',
            'notes.max' => 'Las notas no deben exceder los 1000 caracteres.',
            'alert_days_before.required' => 'Los días de alerta son obligatorios.',
            'alert_days_before.integer' => 'Los días de alerta deben ser un número entero.',
            'alert_days_before.min' => 'Los días de alerta no pueden ser negativos.',
        ];
    }
}
