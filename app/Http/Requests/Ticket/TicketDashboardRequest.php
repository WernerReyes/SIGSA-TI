<?php

namespace App\Http\Requests\Ticket;

use App\Enums\Ticket\TicketCategory;
use App\Enums\Ticket\TicketStatus;
use App\Enums\Ticket\TicketType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketDashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'responsible_id' => ['nullable', 'integer', 'exists:ost_staff,staff_id'],
            'requester_id' => ['nullable', 'integer', 'exists:ost_staff,staff_id'],
            'status' => ['nullable', Rule::enum(TicketStatus::class)],
            'type' => ['nullable', Rule::enum(TicketType::class)],
            'category' => ['nullable', Rule::enum(TicketCategory::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'end_date.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha inicial.',
            'responsible_id.exists' => 'El responsable seleccionado no existe.',
            'requester_id.exists' => 'El solicitante seleccionado no existe.',
            'status.enum' => 'El estado del ticket no es válido.',
            'type.enum' => 'El tipo de ticket no es válido.',
            'category.enum' => 'La categoría de ticket no es válida.',
        ];
    }
}
