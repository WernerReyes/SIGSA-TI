<?php
namespace App\Services;

use App\Models\AssetType;
class AssetTypeService
{
    public function getTypes()
    {
        return AssetType::select('id', 'name', 'is_accessory')->isFromRRHH()->latest()->get();
    }

}