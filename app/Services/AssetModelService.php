<?php

namespace App\Services;

use App\Models\AssetModel;

class AssetModelService
{
    public function getModels()
    {
        return AssetModel::select('id', 'name', 'brand_id', 'asset_type_id', 'created_at', 'updated_at')
            ->with(['brand:id,name', 'type:id,name'])
            ->latest()
            ->get();
    }

    public function getBasicInfo(?int $brand_id, ?int $type_id = null)
    {
        $query = AssetModel::select('id', 'name', 'brand_id', 'asset_type_id');
        
        if ($brand_id !== null) {
            $query->where('brand_id', $brand_id);
        }

        if ($type_id !== null) {
            $query->where('asset_type_id', $type_id);
        }

        return $query->latest()->get();
    }
}
