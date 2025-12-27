<?php
namespace App\Services;

use App\DTOs\Asset\StoreAssetDto;
use App\Models\AssetType;


class AssetService
{

    public function getTypes()
    {
        return AssetType::select('id', 'name')->get();
    }

    public function registerType(string $name)
    {
        $assetType = AssetType::firstOrCreate(['name' => $name]);
        return $assetType;
    }

    public function storeAsset(StoreAssetDto $dto)
    {


    }
}