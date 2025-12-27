<?php

namespace App\Http\Requests\Asset;

use App\Enums\Asset\AssetStatus;
use App\Enums\Asset\AssetType;
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
            'status' => ['required', 'in:' . implode(',', AssetStatus::values())],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'warranty_expiration' => ['nullable', 'date'],
            // 'assigned_to' => ['nullable', 'exists:ost_staff,staff_id'],
            //
        ];
    }
}
