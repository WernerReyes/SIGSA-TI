<?php

namespace App\Http\Requests\Setting;

use App\Enums\Ticket\TicketPriority;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSlaRequest extends FormRequest
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
            'slas' => ['required', 'array', 'min:1'],
            'slas.*.priority' => ['required', 'distinct', 'in:' . TicketPriority::implodeValues()],
            'slas.*.response_time_minutes' => ['required', 'integer', 'min:1'],
            'slas.*.resolution_time_minutes' => ['required', 'integer', 'min:1'],
        ];


    }

    public function messages(): array
    {
        return [
            'slas.required' => 'Se requiere al menos una configuración de SLA.',
            'slas.array' => 'El formato de las configuraciones de SLA es inválido.',
            'slas.min' => 'Se requiere al menos una configuración de SLA.',
            'slas.*.priority.required' => 'El nombre de la prioridad es obligatorio.',
            'slas.*.priority.distinct' => 'Los nombres de las prioridades deben ser únicos.',
            'slas.*.priority.in' => 'El nombre de la prioridad debe ser uno de los siguientes: ' . TicketPriority::implodeValues(', ') . '.',
            'slas.*.response_time_minutes.required' => 'El tiempo de respuesta es obligatorio.',
            'slas.*.response_time_minutes.integer' => 'El tiempo de respuesta debe ser un número entero.',
            'slas.*.response_time_minutes.min' => 'El tiempo de respuesta debe ser al menos 1 minuto.',
            'slas.*.resolution_time_minutes.required' => 'El tiempo de resolución es obligatorio.',
            'slas.*.resolution_time_minutes.integer' => 'El tiempo de resolución debe ser un número entero.',
            'slas.*.resolution_time_minutes.min' => 'El tiempo de resolución debe ser al menos 1 minuto.',
        ];
    }
}
