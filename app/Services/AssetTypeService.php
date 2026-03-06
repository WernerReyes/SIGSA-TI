<?php
namespace App\Services;

use App\Models\Brand;
use App\Models\AssetType;
class AssetTypeService
{
    public function getTypes()
    {
        return AssetType::select('id', 'name', 'doc_category', 'created_at', 'updated_at', 'is_deletable')
            ->isFromRRHH()
            ->latest()
            ->get();
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

        return Brand::select('id', 'name', 'type_id')
            ->where('type_id', $type_id)
            ->orderByDesc('created_at')
            ->get();
    }

}