<?php

namespace App\Http\Requests\Asset;

use App\Enums\DeliveryRecord\DeliveryRecordType;
use Illuminate\Foundation\Http\FormRequest;

class SendDeliveryRecordEmailRequest extends FormRequest
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
            'document_type' => 'required|in:' . implode(',', DeliveryRecordType::values()),
            'email_to' => ['required', 'string', 'max:2000', 'regex:/^\s*[^\s@,;]+@[^\s@,;]+\.[^\s@,;]+\s*(;\s*[^\s@,;]+@[^\s@,;]+\.[^\s@,;]+\s*)*$/'],
            'email_cc' => ['nullable', 'string', 'max:2000', 'regex:/^\s*[^\s@,;]+@[^\s@,;]+\.[^\s@,;]+\s*(;\s*[^\s@,;]+@[^\s@,;]+\.[^\s@,;]+\s*)*$/'],
            'message' => 'nullable|string|max:12000',
            'message_sections' => 'required|array',
            'message_sections.greeting' => 'required|string|max:500',
            'message_sections.intro_paragraph' => 'required|string|max:2000',
            'message_sections.details_intro' => 'required|string|max:1000',
            'message_sections.asset_title' => 'required|string|max:255',
            'message_sections.asset_name' => 'required|string|max:255',
            'message_sections.serial' => 'nullable|string|max:255',
            'message_sections.accessories_title' => 'required|string|max:255',
            'message_sections.accessories' => 'nullable|array',
            'message_sections.accessories.*' => 'string|max:255',
            'message_sections.closing_paragraph' => 'required|string|max:2000',
            'message_sections.signature_label' => 'required|string|max:255',
            'message_sections.signature_area' => 'required|string|max:255',
            'extra_images' => 'nullable|array|max:10',
            'extra_images.*' => 'file|image|mimes:jpg,jpeg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'document_type.required' => 'Debes seleccionar qué documento enviar.',
            'document_type.in' => 'El tipo de documento seleccionado no es válido.',
            'email_to.required' => 'El correo destino es obligatorio.',
            'email_to.regex' => 'Usa correos válidos separados únicamente por punto y coma (;). No uses comas.',
            'email_cc.regex' => 'En copia (CC) usa correos válidos separados únicamente por punto y coma (;). No uses comas.',
            'message.max' => 'El mensaje no puede exceder 5000 caracteres.',
            'message_sections.required' => 'El contenido del correo es obligatorio.',
            'message_sections.array' => 'El contenido del correo debe enviarse con formato válido.',
            'extra_images.array' => 'Las imágenes adicionales deben enviarse como un arreglo.',
            'extra_images.max' => 'Solo puedes agregar hasta 10 imágenes adicionales.',
            'extra_images.*.image' => 'Los adjuntos extra deben ser imágenes.',
            'extra_images.*.mimes' => 'Las imágenes extra deben ser de tipo: jpg, jpeg o png.',
            'extra_images.*.max' => 'Cada imagen extra puede pesar hasta 5MB.',
        ];
    }
}
