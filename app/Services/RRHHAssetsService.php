<?php
namespace App\Services;

use App\Models\Asset;
use App\Enums\Asset\AssetTypeEnum;

class RRHHAssetsService
{
    public function getRRHHAssets()
    {
        return Asset::whereIn('type_id', AssetTypeEnum::RRHHTypes())->get();
    }

    public function getStats()
    {
        $assets = $this->getRRHHAssets();
        $total = $assets->count();
        $celulares = $assets->where('type_id', AssetTypeEnum::CELL_PHONE)->count();
        $cargadores = $assets->where('type_id', AssetTypeEnum::CELL_PHONE_CHARGER)->count();
        return [
            'total' => $total,
            'celulares' => $celulares,
            'cargadores' => $cargadores,
        ];
    }
}
