<?php

namespace App\Http\Requests\Ticket;

use App\Enums\Ticket\TicketCategory;
use App\Enums\Ticket\TicketStatus;
use App\Enums\Ticket\TicketType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketDashboardRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $aliases = [
            'responsible_ids' => 'responsible_id',
            'requester_ids' => 'requester_id',
            'statuses' => 'status',
            'types' => 'type',
            'categories' => 'category',
        ];
        $normalized = [];

        foreach ($aliases as $plural => $singular) {
            if (! $this->has($plural) && $this->has($singular)) {
                $value = $this->input($singular);
                $normalized[$plural] = is_array($value) ? $value : [$value];
            }
        }

        $this->merge($normalized);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'responsible_ids' => ['nullable', 'array'],
            'responsible_ids.*' => ['integer', 'distinct', 'exists:ost_staff,staff_id'],
            'requester_ids' => ['nullable', 'array'],
            'requester_ids.*' => ['integer', 'distinct', 'exists:ost_staff,staff_id'],
            'statuses' => ['nullable', 'array'],
            'statuses.*' => ['string', 'distinct', Rule::enum(TicketStatus::class)],
            'types' => ['nullable', 'array'],
            'types.*' => ['string', 'distinct', Rule::enum(TicketType::class)],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string', 'distinct', Rule::enum(TicketCategory::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'end_date.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha inicial.',
            'responsible_ids.array' => 'Los responsables deben enviarse como una lista.',
            'responsible_ids.*.exists' => 'Uno de los responsables seleccionados no existe.',
            'requester_ids.array' => 'Los solicitantes deben enviarse como una lista.',
            'requester_ids.*.exists' => 'Uno de los solicitantes seleccionados no existe.',
            'statuses.array' => 'Los estados deben enviarse como una lista.',
            'statuses.*.enum' => 'Uno de los estados seleccionados no es válido.',
            'types.array' => 'Los tipos deben enviarse como una lista.',
            'types.*.enum' => 'Uno de los tipos seleccionados no es válido.',
            'categories.array' => 'Las categorías deben enviarse como una lista.',
            'categories.*.enum' => 'Una de las categorías seleccionadas no es válida.',
        ];
    }
}
