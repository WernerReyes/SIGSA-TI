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
}
