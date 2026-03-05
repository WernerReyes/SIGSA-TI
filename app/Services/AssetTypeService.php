<?php
namespace App\Services;

use App\Models\Brand;
use App\Models\AssetType;
class AssetTypeService
{
    public function getTypes()
    {
        return AssetType::select('id', 'name', 'doc_category', 'created_at', 'updated_at', 'is_deletable')
            ->with('brands:id')
            ->isFromRRHH()
            ->latest()
            ->get()
            ->map(function (AssetType $type) {
                $type->setAttribute('brand_ids', $type->brands->pluck('id')->values()->all());
                return $type;
            });
    }

    public function getBasicTypes()
    {  
       
        return AssetType::select('id', 'name', 'doc_category')->isFromRRHH()->latest()->get();
    }

    public function getBrandsForType(?int $type_id)
    {
        if (!$type_id) {
            return Brand::select('id', 'name')->orderByDesc('created_at')->get();
        }

        return Brand::select('brands.id', 'brands.name')
            ->whereHas('types', function ($query) use ($type_id) {
                $query->where('assets_type.id', $type_id);
            })
            ->orderByDesc('brands.created_at')
            ->get();
    }

}