<?php

namespace App\Http\Requests\AdminControl;

use App\Enums\contract\BillingFrequency;
use App\Enums\Contract\ContractPeriod;
use App\Enums\Contract\ContractStatus;
use App\Enums\Contract\ContractType;
use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'type' => 'required|string|in:' . ContractType::implodeValues(),
            'period' => 'required|string|in:' . ContractPeriod::implodeValues(),
            'status' => 'required|string|in:' . ContractStatus::implodeValues(),
            'start_date' => 'required|date',
            'end_date' => 'required_if:period,' . ContractPeriod::FIXED_TERM->value . '|nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:1000',

            'frequency' => 'required_if:period,' . ContractPeriod::RECURRING->value . '|nullable|string|in:' . BillingFrequency::implodeValues(),
            'amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'auto_renew' => 'nullable|boolean',
            'next_billing_date' => 'required_if:period,' . ContractPeriod::RECURRING->value . '|nullable|date',
            'billing_cycle_days' => 'required_if:period,' . ContractPeriod::RECURRING->value . '|nullable|integer|min:0',

        ];
    }

    
}
