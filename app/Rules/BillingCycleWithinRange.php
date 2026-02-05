<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BillingCycleWithinRange implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $start = request()->input('start_date');
        $end = request()->input('end_date');

        if (!$start || !$end) {
            return; // se valida en otras reglas
        }

        $startDate = new \DateTime($start);
        $endDate = new \DateTime($end);
        $diffDays = $startDate->diff($endDate)->days;

        if ($value > $diffDays) {
            $fail($attribute, 'El ciclo de facturación debe estar dentro del rango de duración del contrato.');
        }
    }
}


// use Illuminate\Contracts\Validation\Rule;
// use Carbon\Carbon;

// class BillingCycleWithinRange implements Rule
// {
//     protected $start;
//     protected $end;

//     public function __construct($start, $end)
//     {
//         $this->start = $start;
//         $this->end = $end;
//     }

//     public function passes($attribute, $value)
//     {
//         if (!$this->start || !$this->end) {
//             return true; // se valida en otras reglas
//         }

//         $startDate = Carbon::parse($this->start);
//         $endDate = Carbon::parse($this->end);
//         $diffDays = $startDate->diffInDays($endDate);

//         return $value <= $diffDays;
//     }

//     public function message()
//     {
//         return 'El ciclo de facturación debe estar dentro del rango de duración del contrato.';
//     }
// }
