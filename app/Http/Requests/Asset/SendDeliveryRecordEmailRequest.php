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
            'email_to' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
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
            'email_to.email' => 'Debes ingresar un correo válido.',
            'message.required' => 'El mensaje es obligatorio.',
            'message.max' => 'El mensaje no puede exceder 5000 caracteres.',
            'extra_images.array' => 'Las imágenes adicionales deben enviarse como un arreglo.',
            'extra_images.max' => 'Solo puedes agregar hasta 10 imágenes adicionales.',
            'extra_images.*.image' => 'Los adjuntos extra deben ser imágenes.',
            'extra_images.*.mimes' => 'Las imágenes extra deben ser de tipo: jpg, jpeg o png.',
            'extra_images.*.max' => 'Cada imagen extra puede pesar hasta 5MB.',
        ];
    }
}
