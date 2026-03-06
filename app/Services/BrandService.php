<?php

namespace App\Services;

use App\Models\Brand;

class BrandService
{
    public function getBrands()
    {
        return Brand::select('id', 'name', 'type_id', 'created_at', 'updated_at')
            ->with('type:id,name')
            ->latest()
            ->get();
    }

    public function getBasicInfo()
    {
        return Brand::select('id', 'name', 'type_id')->latest()->get();
    }

    public function getBrandModels(?int $brand_id)
    {
        if (!$brand_id) {
            return collect();
        }

        return Brand::findOrFail($brand_id)
            ->models()
            ->select('models.id', 'models.name', 'models.brand_id')
            ->orderBy('models.created_at', 'desc')
            ->get();
    }
}
