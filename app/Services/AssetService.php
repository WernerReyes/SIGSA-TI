<?php
namespace App\Services;

use App\DTOs\Asset\AssignAssetDto;
use App\DTOs\Asset\StoreAssetDto;
use App\DTOs\Asset\UpdateAssetDto;
use App\Enums\Asset\AssetStatus;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetHistory;
use App\Models\AssetType;

use DB;
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
        return Asset::with(

            'type:id,name',
            // 'assignedTo:staff_id,firstname,lastname,dept_id',
            // 'assignedTo.department:id,name',
            'assignment.assignedTo:staff_id,firstname,lastname',
            'assignment.assignedTo.department:id,name',
        )->get();
    }

    public function storeAsset(StoreAssetDto $dto)
    {

        $asset = DB::transaction(function () use ($dto) {

            $asset = Asset::create([
                'name' => $dto->name,
                'type_id' => $dto->type_id,
                'status' => $dto->status,
                'brand' => $dto->brand,
                'model' => $dto->model,
                'serial_number' => $dto->serial_number,
                'purchase_date' => $dto->purchase_date,
                'warranty_expiration' => $dto->warranty_expiration,
                'is_new' => $dto->is_new,

            ]);

            AssetHistory::create([
                'action' => 'Creación de activo',
                'asset_id' => $asset->id,
                'performed_by' => auth()->user()->staff_id,
                'performed_at' => now(),
            ]);

            return $asset;
        });


        return $asset;

    }

    public function updateAsset(UpdateAssetDto $dto)
    {
        $asset = Asset::find($dto->id);
        if (!$asset) {
            throw new NotFoundHttpException('No se encontró el activo');
        }


        $asset = DB::transaction(function () use ($dto, $asset) {


            $fieldChanges = [];
            foreach ($dto as $key => $value) {
                if ($key === 'id') {
                    continue;
                }
                $formatDateFields = ['purchase_date', 'warranty_expiration'];
                if (in_array($key, $formatDateFields)) {
                    $value = $value ? date('Y-m-d', strtotime($value)) : null;
                    $assetValue = $asset->$key ? date('Y-m-d', strtotime($asset->$key)) : null;
                    if ($value !== null && $assetValue != $value) {
                        $fieldChanges[] = $this->fromKeyToLabel($key) . " cambiado de '{$assetValue}' a '$value'";
                    }
                    continue;
                }

                if ($key === 'status') {
                    if ($value !== null && $asset->$key != $value) {
                        $fieldChanges[] = $this->fromKeyToLabel($key) . " cambiado de '" . $this->fromStatusToLabel($asset->$key) . "' a '" . $this->fromStatusToLabel($value) . "'";
                    }
                    continue;
                }

                if ($value !== null && $asset->$key != $value) {
                    $fieldChanges[] = $this->fromKeyToLabel($key) . " cambiado de '{$asset->$key}' a '$value'";
                }
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
                'is_new' => $dto->is_new ?? $asset->is_new,
            ]);

            if ($dto->status !== AssetStatus::ASSIGNED->value) {
                $assignment = $asset->assignment;
                if ($assignment) {
                    $assignment->delete();
                }
            }


            AssetHistory::create([
                'action' => 'Actualización de activo: ' . implode(', ', $fieldChanges),
                'asset_id' => $asset->id,
                'performed_by' => auth()->user()->staff_id,
                'performed_at' => now(),
            ]);

            return $asset;
        });





        return $asset;
    }

    private function fromKeyToLabel(string $key): string
    {
        $mapping = [
            'name' => 'Nombre',
            'type_id' => 'Tipo',
            'status' => 'Estado',
            'brand' => 'Marca',
            'model' => 'Modelo',
            'serial_number' => 'Número de serie',
            'purchase_date' => 'Fecha de compra',
            'warranty_expiration' => 'Vencimiento de garantía',
            'is_new' => 'Es nuevo',
        ];

        return $mapping[$key] ?? $key;
    }

    private function fromStatusToLabel(string $status): string
    {
        $mapping = [
            AssetStatus::AVAILABLE->value => 'Disponible',
            AssetStatus::ASSIGNED->value => 'Asignado',
            AssetStatus::IN_REPAIR->value => 'En reparación',
            AssetStatus::DECOMMISSIONED->value => 'Dado de baja',
        ];

        return $mapping[$status] ?? $status;
    }


    public function assignAsset(AssignAssetDto $dto)
    {


        DB::transaction(function () use ($dto) {


            $asset = Asset::find($dto->asset_id);
            $asset->status = AssetStatus::ASSIGNED->value;
            // $asset->assigned_to_id = $dto->assigned_to_id;
            $asset->save();

            $assigned = AssetAssignment::updateOrCreate(
                ['asset_id' => $dto->asset_id],
                [
                    'assigned_to_id' => $dto->assigned_to_id,
                    'assigned_at' => now(),
                    'comment' => $dto->comment,
                ]
            );

            AssetHistory::create([
                'action' => "Activo asignado a {$assigned->assignedTo->full_name}",
                'asset_id' => $dto->asset_id,
                'performed_by' => auth()->user()->staff_id,
                'performed_at' => now(),
            ]);




        });
    }
}