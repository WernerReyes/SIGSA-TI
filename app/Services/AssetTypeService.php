<?php
namespace App\Services;

use App\Models\AssetType;
class AssetTypeService
{
    public function getTypes()
    {
        return AssetType::select('id', 'name', 'doc_category', 'created_at', 'updated_at', 'is_deletable')->isFromRRHH()->latest()->get();
    }

    public function getBasicTypes()
    {  
       
        return AssetType::select('id', 'name', 'doc_category')->isFromRRHH()->latest()->get();
    }

}