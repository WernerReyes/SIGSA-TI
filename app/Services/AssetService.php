<?php
namespace App\Services;

use App\DTOs\Asset\StoreAssetDto;
use App\DTOs\Asset\UpdateAssetDto;
use App\Models\Asset;
use App\Models\AssetType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class AssetService
{

    public function getTypes()
    {
        return AssetType::select('id', 'name')->get();
    }

    public function registerType(string $name, ?int $id = null)
    {
        if ($id) {
            $assetType = $this->findTypeById($id);
            $assetType->name = $name;
            $assetType->save();
            return $assetType;
        }

        $assetType = AssetType::firstOrCreate(['name' => $name]);
        return $assetType;
    }


    public function deleteType(int $id)
    {
        $assetType = $this->findTypeById($id);
        $assetType->delete();
    }

    private function findTypeById(int $id): AssetType
    {
        $assetType = AssetType::find($id);
        if (!$assetType) {
            throw new NotFoundHttpException('No se encontró el tipo de activo');
        }
        return $assetType;
    }


    public function getAll()
    {
        return Asset::with('assignedTo:staff_id,firstname,lastname')->get();
    }

    public function storeAsset(StoreAssetDto $dto)
    {
        $asset = Asset::create([
            'name' => $dto->name,
            'type_id' => $dto->type_id,
            'status' => $dto->status,
            'brand' => $dto->brand,
            'model' => $dto->model,
            'serial_number' => $dto->serial_number,
            'purchase_date' => $dto->purchase_date,
            'warranty_expiration' => $dto->warranty_expiration,
            'assigned_to_id' => $dto->assigned_to,
        ]);

        return $asset;

    }

    public function updateAsset(UpdateAssetDto $dto)
    {
        $asset = Asset::find($dto->id);
        if (!$asset) {
            throw new NotFoundHttpException('No se encontró el activo');
        }

        $asset->update([
            'name' => $dto->name ?? $asset->name,
            'type_id' => $dto->type_id ?? $asset->type_id,
            'status' => $dto->status ?? $asset->status,
            'brand' => $dto->brand ?? $asset->brand,
            'model' => $dto->model ?? $asset->model,
            'serial_number' => $dto->serial_number ?? $asset->serial_number,
            'purchase_date' => $dto->purchase_date ?? $asset->purchase_date,
            'warranty_expiration' => $dto->warranty_expiration ?? $asset->warranty_expiration,
            'assigned_to_id' => $dto->assigned_to ?? $asset->assigned_to_id,
        ]);

        return $asset;
    }
}