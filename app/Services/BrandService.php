<?php

namespace App\Services;

use App\Models\Brand;

class BrandService
{
    public function getBrands()
    {
        return Brand::select('id', 'name', 'created_at', 'updated_at')->latest()->get();
    }

    public function getBasicInfo()
    {
        return Brand::select('id', 'name')->latest()->get();
    }

    public function getBrandModels(?int $brand_id, ?int $type_id = null)
    {
        if (!$brand_id) {
            return collect();
        }

        ds($brand_id, $type_id);

        return Brand::findOrFail($brand_id)
            ->models()
            ->select('models.id', 'models.name', 'models.brand_id', 'models.asset_type_id')
            ->when($type_id, fn($query) => $query->where('asset_type_id', $type_id))
            ->orderBy('models.created_at', 'desc')
            ->get();
    }
}
