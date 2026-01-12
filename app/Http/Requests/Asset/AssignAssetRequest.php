<?php

namespace App\Http\Requests\Asset;

use Illuminate\Foundation\Http\FormRequest;

class AssignAssetRequest extends FormRequest
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
            'assign_date' => ['required', 'date'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'asset_id' => ['required', 'integer', 'exists:assets,id'],
            'assigned_to_id' => ['required', 'integer', 'exists:ost_staff,staff_id'],
            'accessories' => ['array'],
            'accessories.*' => ['integer', 'exists:assets,id'],
        ];
    }
}
