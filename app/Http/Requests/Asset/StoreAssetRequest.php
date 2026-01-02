<?php

namespace App\Http\Requests\Asset;

use App\Enums\Asset\AssetStatus;
use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'type_id' => ['exists:assets_type,id'],
            // 'status' => ['required', 'in:' . implode(',', AssetStatus::values([AssetStatus::ASSIGNED->value]))],
            'color' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'processor' => ['nullable', 'string', 'max:255'],
            'ram' => ['nullable', 'string', 'max:255'],
            'storage' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'imei' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'warranty_expiration' => ['nullable', 'date'],
            'is_new' => ['boolean'],
            
            // 'assigned_to' => ['nullable', 'exists:ost_staff,staff_id'],
            //
        ];
    }


    public function messages(): array
    {
        return [
            'type_id.exists' => 'El tipo de activo seleccionado no es válido.',
            // 'status.in' => 'El estado del activo seleccionado no es válido.',
            
            // Otros mensajes personalizados...
        ];
    }
}
