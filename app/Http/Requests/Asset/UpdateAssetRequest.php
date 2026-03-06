<?php

namespace App\Http\Requests\Asset;
use App\Enums\Asset\AssetStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

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
            // 'id' => ['required', 'integer'],
            'name' => ['nullable', 'string', 'max:255'],
            'type_id' => ['nullable', 'exists:assets_type,id'],
            // 'status' => ['nullable', 'in:' . implode(',', AssetStatus::values())],
            'color' => ['nullable', 'string', 'max:100'],
            // 'brand' => ['nullable', 'string', 'max:255'],
            // 'model' => ['nullable', 'string', 'max:255'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'model_id' => ['nullable', 'exists:models,id'],
            
            'serial_number' => ['nullable', 'string', 'max:255'],
            'processor' => ['nullable', 'string', 'max:255'],
            'ram' => ['nullable', 'string', 'max:255'],
            'storage' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'imei' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'warranty_expiration' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'is_new' => ['boolean'],
            
            //
        ];

        
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $asset = $this->route('asset');

            $typeId = $this->input('type_id') ?? $asset?->type_id;
            $brandId = $this->input('brand_id') ?? $asset?->brand_id;
            $modelId = $this->input('model_id') ?? $asset?->model_id;

            if ($typeId && $brandId) {
                $brandBelongsToType = DB::table('asset_type_brand')
                    ->where('asset_type_id', $typeId)
                    ->where('brand_id', $brandId)
                    ->exists();

                if (!$brandBelongsToType) {
                    $validator->errors()->add('brand_id', 'La marca seleccionada no está asociada al tipo elegido.');
                }
            }

            if ($modelId) {
                $modelBelongsToSelection = DB::table('models')
                    ->where('id', $modelId)
                    ->where('brand_id', $brandId)
                    ->where('asset_type_id', $typeId)
                    ->exists();

                if (!$modelBelongsToSelection) {
                    $validator->errors()->add('model_id', 'El modelo seleccionado no corresponde al tipo y marca elegidos.');
                }
            }
        });
    }
}
