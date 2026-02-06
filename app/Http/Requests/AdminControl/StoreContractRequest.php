<?php

namespace App\Http\Requests\AdminControl;

use App\Enums\Contract\BillingFrequency;
use App\Enums\Contract\ContractPeriod;
use App\Enums\Contract\ContractStatus;
use App\Enums\Contract\ContractType;

use App\Rules\AlertDaysBeforeWithinRange;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

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
            'alert_days_before' => [
                // 'required_if:period,' . ContractPeriod::RECURRING->value,
                Rule::requiredIf(function () {
                    $period = $this->input('period');
                    $autoRenew = $this->input('auto_renew');
                    return ($period === ContractPeriod::RECURRING->value && $autoRenew === false) || $period === ContractPeriod::FIXED_TERM->value || ($period === ContractPeriod::ONE_TIME->value && $this->input('has_warranty') === true);
                }),
                'nullable',
                'integer',
                'min:0',
                new AlertDaysBeforeWithinRange()
            ],

        ];
    }

    public function messages(): array
    {
        return [
            'end_date.required_if' => 'La fecha de finalización es obligatoria para contratos de plazo fijo.',
            'next_billing_date.required_if' => 'La próxima fecha de facturación es obligatoria para contratos recurrentes.',
            'alert_days_before.required_if' => 'Los días para la alerta son obligatorios para contratos recurrentes y de plazo fijo.',
        ];
    }


}
