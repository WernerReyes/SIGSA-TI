<?php

namespace App\Services;

use App\Models\AssetModel;

class AssetModelService
{
    public function getModels()
    {
        return AssetModel::select('id', 'name', 'created_at', 'updated_at')->latest()->get();
    }

    public function getBasicInfo()
    {
        return AssetModel::select('id', 'name')->latest()->get();
    }
}
