<?php

namespace App\Http\Requests\asset;

use App\Enums\AssetAssignment\ReturnReason;
use Illuminate\Foundation\Http\FormRequest;

class DevolveAssetRequest extends FormRequest
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
            'return_comment' => 'nullable|string|max:500',
            // 'responsible_id' => 'required|exists:ost_staff,staff_id',
            'return_date' => 'required|date',
            'return_reason' => 'required|in:' . implode(',', ReturnReason::values()),
            // 'accessories' => 'nullable|array',
            // 'accessories.*' => 'integer|exists:assets,id',
            'ticket_id' => 'nullable|exists:tickets_sistema,id',
        ];
    }

    public function messages(): array
    {
        return [
            'return_comment.string' => 'El comentario de devolución debe ser una cadena de texto.',
            'return_comment.max' => 'El comentario de devolución no debe exceder los 500 caracteres.',
            // 'responsible_id.required' => 'El ID del responsable es obligatorio.',
            // 'responsible_id.exists' => 'El ID del responsable proporcionado no existe.',
            'return_date.required' => 'La fecha de devolución es obligatoria.',
            'return_date.date' => 'La fecha de devolución debe ser una fecha válida.',
            'return_reason.required' => 'El motivo de devolución es obligatorio.',
            'return_reason.in' => 'El motivo de devolución seleccionado no es válido.',
            // 'accessories.array' => 'Los accesorios deben ser un arreglo.',
            // 'accessories.*.integer' => 'Cada accesorio debe ser un ID válido.',
            // 'accessories.*.exists' => 'El accesorio con el ID proporcionado no existe.',
            'ticket_id.exists' => 'El ticket con el ID proporcionado no existe.',
        ];
    }
}
