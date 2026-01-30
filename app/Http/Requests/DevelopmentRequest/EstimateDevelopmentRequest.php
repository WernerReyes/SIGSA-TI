<?php

namespace App\Http\Requests\DevelopmentRequest;

use App\Enums\User\UserCharge;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Inertia\Inertia;

class EstimateDevelopmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return UserCharge::includes($this->user()->id_cargo);
    }

    protected function failedAuthorization()
    {
        Inertia::flash('error', 'No estás autorizado para realizar esta acción.');
        throw new HttpResponseException(
            back()
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'estimated_hours' => 'required|integer|min:1',
            'estimated_end_date' => 'required|date|after_or_equal:today',
            // 'people_amount' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'estimated_hours.required' => 'Las horas estimadas son obligatorias.',
            'estimated_hours.integer' => 'Las horas estimadas deben ser un número entero.',
            'estimated_hours.min' => 'Las horas estimadas deben ser al menos 1.',

            'estimated_end_date.required' => 'La fecha estimada de finalización es obligatoria.',
            'estimated_end_date.date' => 'La fecha estimada de finalización debe ser una fecha válida.',
            'estimated_end_date.after_or_equal' => 'La fecha estimada de finalización debe ser hoy o una fecha futura.',

            // 'people_amount.required' => 'La cantidad de personas es obligatoria.',
            // 'people_amount.integer' => 'La cantidad de personas debe ser un número entero.',
            // 'people_amount.min' => 'La cantidad de personas debe ser al menos 1.',
        ];
    }
}
