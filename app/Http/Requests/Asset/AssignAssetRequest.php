<?php

namespace App\Http\Requests\Asset;

use Illuminate\Foundation\Http\FormRequest;

class AssignAssetRequest extends FormRequest
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
            'assign_date' => ['required', 'date', 'before_or_equal:today'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'asset_id' => ['required', 'integer', 'exists:assets,id'],
            'ticket_id' => ['nullable', 'integer', 'exists:tickets_sistema,id'],
            'assigned_to_id' => ['required', 'integer', 'exists:ost_staff,staff_id'],
            'responsible_id' => ['nullable', 'integer', 'exists:ost_staff,staff_id'],
            'accessories' => ['array'],
            'accessories.*' => ['integer', 'exists:assets,id'],
        ];

    }

    public function messages(): array
    {
        return [
            'assign_date.required' => 'La fecha de asignación es obligatoria.',
            'assign_date.date' => 'La fecha de asignación debe ser una fecha válida.',
            'assign_date.before_or_equal' => 'La fecha de asignación no puede ser futura.',
            'comment.string' => 'El comentario debe ser una cadena de texto.',
            'comment.max' => 'El comentario no puede exceder los 1000 caracteres.',
            'asset_id.required' => 'El activo es obligatorio.',
            'asset_id.integer' => 'El activo debe ser un número entero válido.',
            'asset_id.exists' => 'El activo seleccionado no existe.',
            'assigned_to_id.required' => 'El usuario asignado es obligatorio.',
            'assigned_to_id.integer' => 'El usuario asignado debe ser un número entero válido.',
            'assigned_to_id.exists' => 'El usuario asignado no existe.',
            'accessories.array' => 'Los accesorios deben ser un arreglo.',
            'accessories.*.integer' => 'Cada accesorio debe ser un número entero válido.',
            'accessories.*.exists' => 'Uno o más accesorios seleccionados no existen.',
            'ticket_id.integer' => 'El ticket debe ser un número entero válido.',
            'ticket_id.exists' => 'El ticket seleccionado no existe.',
            'responsible_id.integer' => 'El responsable debe ser un número entero válido.',
            'responsible_id.exists' => 'El responsable seleccionado no existe.',
        ];
    }
}
