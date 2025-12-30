<?php

namespace App\Http\Requests\Asset;
use App\Enums\Asset\AssetStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetRequest extends FormRequest
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
            'id' => ['required', 'integer'],
            'name' => ['nullable', 'string', 'max:255'],
            'type_id' => ['nullable', 'exists:assets_type,id'],
            'status' => ['nullable', 'in:' . implode(',', AssetStatus::values())],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'warranty_expiration' => ['nullable', 'date'],
            'is_new' => ['boolean'],
            
            //
        ];
    }
}
