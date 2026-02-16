<?php

namespace App\Http\Requests\Ticket;


use App\Enums\Ticket\TicketCategory;
use App\Enums\Ticket\TicketImpact;
use App\Enums\Ticket\TicketType;
use App\Enums\Ticket\TicketUrgency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
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

            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
            'type' => ['required', 'in:' . TicketType::implodeValues()],

            'images' => ['sometimes', 'array', 'max:5'],
            'images.*' => ['file', 'image', 'max:2048'], //
            'impact' => ['required', 'in:' . TicketImpact::implodeValues()],
            'urgency' => ['required', 'in:' . TicketUrgency::implodeValues()],
            'category' => [
                'nullable',
                'required_if:type,' . TicketType::SERVICE_REQUEST->value,
                'in:' . TicketCategory::implodeValues(),
            ],
            'requester_id' => [
                Rule::requiredIf($this->is('api/*')),
                'nullable',
                'exists:ost_staff,staff_id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.min' => 'El título debe tener al menos :min caracteres.',
            'title.max' => 'El título no debe exceder de :max caracteres.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'description.min' => 'La descripción debe tener al menos :min caracteres.',
            'description.max' => 'La descripción no debe exceder de :max caracteres.',


            'type.required' => 'El tipo de ticket es obligatorio.',
            'type.in' => 'El tipo de ticket seleccionado no es válido.',


            'images.array' => 'Las imágenes deben ser un arreglo.',
            'images.max' => 'No se pueden subir más de :max imágenes.',
            'images.*.file' => 'Cada imagen debe ser un archivo.',
            'images.*.image' => 'Cada archivo debe ser una imagen.',
            'images.*.max' => 'Cada imagen no debe exceder de :max kilobytes.',


            'impact.required' => 'El impacto del ticket es obligatorio.',
            'impact.in' => 'El impacto del ticket seleccionado no es válido.',

            'urgency.required' => 'La urgencia del ticket es obligatoria.',
            'urgency.in' => 'La urgencia del ticket seleccionada no es válida.',

            'category.required_if' => 'La categoría es obligatoria para este tipo de ticket.',
            'category.in' => 'La categoría seleccionada no es válida.',
            
            'requester_id.required' => 'El ID del solicitante es obligatorio para solicitudes API.',

            'requester_id.exists' => 'El solicitante seleccionado no existe.',
        ];
    }
}
