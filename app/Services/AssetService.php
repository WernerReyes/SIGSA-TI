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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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
            'assignment.assignedTo:staff_id,firstname,lastname,dept_id',
            'assignment.assignedTo.department:id,name',
        )->get();
    }

    public function storeAsset(StoreAssetDto $dto)
    {

        $asset = DB::transaction(function () use ($dto) {

            $asset = Asset::create([
                'name' => $dto->name,
                'type_id' => $dto->type_id,
                // 'status' => $dto->status,
                'color' => $dto->color,
                'status' => AssetStatus::AVAILABLE->value,
                'brand' => $dto->brand,
                'model' => $dto->model,
                'serial_number' => $dto->serial_number,
                'processor' => $dto->processor,
                'ram' => $dto->ram,
                'storage' => $dto->storage,
                'phone' => $dto->phone,
                'imei' => $dto->imei,
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
                // 'status' => $dto->status ?? $asset->status,
                'color' => $dto->color ?? $asset->color,
                'brand' => $dto->brand ?? $asset->brand,
                'model' => $dto->model ?? $asset->model,
                'serial_number' => $dto->serial_number ?? $asset->serial_number,
                'processor' => $dto->processor ?? $asset->processor,
                'ram' => $dto->ram ?? $asset->ram,
                'storage' => $dto->storage ?? $asset->storage,
                'phone' => $dto->phone ?? $asset->phone,
                'imei' => $dto->imei ?? $asset->imei,
                'purchase_date' => $dto->purchase_date ?? $asset->purchase_date,
                'warranty_expiration' => $dto->warranty_expiration ?? $asset->warranty_expiration,
                'is_new' => $dto->is_new ?? $asset->is_new,
            ]);

            // if ($dto->status !== AssetStatus::ASSIGNED->value) {
            //     $assignment = $asset->assignment;
            //     if ($assignment) {
            //         $assignment->delete();
            //     }
            // }


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
            'processor' => 'Procesador',
            'ram' => 'RAM',
            'storage' => 'Almacenamiento',
            'purchase_date' => 'Fecha de compra',
            'phone' => 'Teléfono',
            'imei' => 'IMEI',
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
                    'assigned_at' => $dto->assign_date,
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

    public function devolveAsset(int $assetId)
    {
        DB::transaction(function () use ($assetId) {

            $asset = Asset::find($assetId);
            if (!$asset) {
                throw new NotFoundHttpException('No se encontró el activo');
            }

            $asset->status = AssetStatus::AVAILABLE->value;
            // $asset->assigned_to_id = null;
            $asset->save();

            $assignment = $asset->assignment;
            if ($assignment) {
                $assignment->delete();
            }

            AssetHistory::create([
                'action' => "Activo devuelto y disponible",
                'asset_id' => $assetId,
                'performed_by' => auth()->user()->staff_id,
                'performed_at' => now(),
            ]);
        });
    }


    public function generateLaptopAssignmentDocument(
        int $assetId
    ) {
        $asset = Asset::find($assetId);
        if (!$asset) {
            throw new NotFoundHttpException('No se encontró el activo');
        }

        $asset->load('assignment.assignedTo');

        if (!$asset->assignment || !$asset->assignment->assignedTo) {
            throw new BadRequestException('El activo no está asignado a ningún usuario');
        }

        Storage::disk('public')->makeDirectory('documents');


        $template = new TemplateProcessor(storage_path('app/templates/cargo-laptop.docx'));

        $template->setValue('assign_date', $asset->assignment->assigned_at->translatedFormat('d \d\e F \d\e\l Y'));
        $template->setValue('fullname', strtoupper($asset->assignment->assignedTo->full_name));
        $template->setValue('dni', $asset->assignment->assignedTo->dni ?? 'N/A');
        $template->setValue('is_new', $asset->is_new ? 'NUEVO' : 'USADO');
        $template->setValue('brand', strtoupper($asset->brand));
        $template->setValue('model', strtoupper($asset->model));
        $template->setValue('serial_number', $asset->serial_number);
        $template->setValue('processor', strtoupper($asset->processor ?? 'N/A'));
        $template->setValue('ram', strtoupper($asset->ram ?? 'N/A'));
        $template->setValue('storage', strtoupper($asset->storage ?? 'N/A'));

        $template->setValue('comment', $asset->assignment->comment ?? 'N/A');


        $fileName = 'cargo_laptop_' . strtolower(str_replace(' ', '_', $asset->assignment->assignedTo->full_name)) . '_' . Carbon::now()->format('Ymd_His') . '.docx';
        $path = storage_path('app/public/documents/' . $fileName);
        $template->saveAs($path);

        return $path;
    }

    public function generateCellphoneAssignmentDocument(
        int $assetId
    ) {
        $asset = Asset::find($assetId);
        if (!$asset) {
            throw new NotFoundHttpException('No se encontró el activo');
        }

        $asset->load('assignment.assignedTo');
        if (!$asset->assignment || !$asset->assignment->assignedTo) {
            throw new BadRequestException('El activo no está asignado a ningún usuario');
        }

        Storage::disk('public')->makeDirectory('documents');

        $template = new TemplateProcessor(storage_path('app/templates/cargo-celular.docx'));


        // 24/11/2025
        $template->setValue('assign_date', $asset->assignment->assigned_at->format('d/m/Y'));
        $template->setValue('fullname', strtoupper($asset->assignment->assignedTo->full_name));
        $template->setValue('dni', $asset->assignment->assignedTo->dni ?? 'N/A');
        $template->setValue('department', strtoupper($asset->assignment->assignedTo->department->name ?? 'N/A'));
        $template->setValue('is_new', $asset->is_new ? 'NUEVO' : 'USADO EN BUEN ESTADO');
        $template->setValue('brand', strtoupper($asset->brand));
        $template->setValue('model', strtoupper($asset->model));
        $template->setValue('comment', $asset->assignment->comment ?? 'N/A');
        $template->setValue('phone', $asset->phone ?? 'N/A');
        $template->setValue('imei', $asset->imei ?? 'N/A');

        $fileName = 'cargo_celular_' . strtolower(str_replace(' ', '_', $asset->assignment->assignedTo->full_name)) . '_' . Carbon::now()->format('Ymd_His') . '.docx';
        $path = storage_path('app/public/documents/' . $fileName);

        $template->saveAs($path);

        return $path;

    }

}