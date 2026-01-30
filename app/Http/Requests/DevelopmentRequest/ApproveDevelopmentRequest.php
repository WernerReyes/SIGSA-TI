<?php

namespace App\Http\Requests\DevelopmentRequest;

use App\Enums\DevelopmentRequest\DevelopmentApprovalLevel;
use App\Enums\DevelopmentRequest\DevelopmentApprovalStatus;
use App\Enums\User\UserCharge;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Inertia\Inertia;

class ApproveDevelopmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
   public function authorize(): bool
    {  
        // UserCharge::includes($this->user()->id_cargo)
        return true;
    }

    protected function failedAuthorization()
    {
        Inertia::flash('error', 'No estás autorizado para realizar esta acción.');
        throw new HttpResponseException(
            back()
        );
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'status' => 'required|in:' . DevelopmentApprovalStatus::implodeValues(),
            // 'level' => 'required|in:' . DevelopmentApprovalLevel::implodeValues(),
            'comments' => 'nullable|string|max:1000',
        ];
    }
}
