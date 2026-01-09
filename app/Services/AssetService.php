<?php
namespace App\Services;

use App\DTOs\Asset\AssetFiltersDto;
use App\DTOs\Asset\AssignAssetDto;
use App\DTOs\Asset\DevolveAssetDto;
use App\DTOs\Asset\StoreAssetDto;
use App\DTOs\Asset\UpdateAssetDto;
use App\DTOs\Asset\UploadDeliveryRecordDto;
use App\Enums\Asset\AssetStatus;
use App\Enums\AssetAssignment\ReturnReason;
use App\Enums\AssetHistory\AssetHistoryAction;
use App\Enums\DeliveryRecord\DeliveryRecordType;
use App\Enums\Department\Allowed;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetHistory;
use App\Models\AssetType;

use App\Models\DeliveryRecord;
use App\Models\User;

use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class AssetService
{

    public function getTypes()
    {
        return AssetType::get();
    }

    public function registerType(string $name, ?int $id = null)
    {
        try {
            if ($id) {
                $assetType = $this->findTypeById($id);
                $assetType->name = $name;
                $assetType->save();
                return $assetType;
            }

            $assetType = AssetType::firstOrCreate(['name' => $name]);
            return $assetType;
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al registrar el tipo de activo');
        }
    }


    public function deleteType(int $id)
    {
        try {
            $assetType = $this->findTypeById($id);
            $assetType->delete();
        } catch (\Exception $e) {
            if ($e->getCode() === '23000') {
                throw new BadRequestException('No se puede eliminar el tipo de activo porque está asociado a uno o más equipos.');
            }
            throw new InternalErrorException('Error al eliminar el tipo de activo');
        }
    }

    private function findTypeById(int $id): AssetType
    {
        try {
            $assetType = AssetType::find($id);
            if (!$assetType) {
                throw new NotFoundHttpException('No se encontró el tipo de activo');
            }
            return $assetType;
        } catch (\Exception $e) {
            if ($e instanceof NotFoundHttpException) {
                throw $e;
            }

            throw new InternalErrorException('Error al buscar el tipo de activo');
        }
    }

    public function getPaginated(AssetFiltersDto $filtersDto)
    {
        try {
            return Asset::query()->with(
                'type:id,name',
                'currentAssignment:id,asset_id,assigned_to_id,assigned_at',
                'currentAssignment.assignedTo:staff_id,firstname,lastname',
            )->
                when($filtersDto->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('brand', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%")
                            ->orWhere('serial_number', 'like', "%{$search}%")
                            ->orWhereHas('currentAssignment.assignedTo', function ($q2) use ($search) {
                                $q2->where('firstname', 'like', "%{$search}%")
                                    ->orWhere('lastname', 'like', "%{$search}%")->orWhere(DB::raw("CONCAT(firstname, ' ', lastname)"), 'like', "%{$search}%");
                                // $q2->orWhere(DB::raw("CONCAT(firstname, ' ', lastname)"), 'like', "%{$search}%");
        
                            });
                    });
                })->when($filtersDto->status && count($filtersDto->status) > 0, function ($query) use ($filtersDto) {
                    $query->whereIn('status', $filtersDto->status);
                })->when($filtersDto->types, function ($query, $typeId) {
                    $query->where('type_id', $typeId);
                })->when($filtersDto->assigned_to, function ($query) use ($filtersDto) {
                    $ids = array_filter($filtersDto->assigned_to);

                    $query->where(function ($q) use ($ids, $filtersDto) {
                        if ($ids) {
                            $q->whereHas(
                                'currentAssignment',
                                fn($q2) =>
                                $q2->whereIn('assigned_to_id', $ids)
                            );
                        }

                        if (in_array(null, $filtersDto->assigned_to, true)) {
                            $q->orWhereDoesntHave('currentAssignment');
                        }
                    });
                })
                ->when($filtersDto->department_id && count($filtersDto->department_id) > 0, function ($query) use ($filtersDto) {
                    $query->where(function ($q) use ($filtersDto) {
                        $q->whereHas('currentAssignment.assignedTo', function ($q2) use ($filtersDto) {
                            $q2->whereIn('dept_id', $filtersDto->department_id);
                        });
                        // $q->orWhereDoesntHave('currentAssignment');
                    });
                })->
                latest()->paginate(10)->withQueryString();

        } catch (\Exception $e) {
            throw new InternalErrorException('Error al obtener los activos');
        }
    }


    public function getPaginated2(AssetFiltersDto $filtersDto)
    {
        try {
            return Asset::query()->with(
                'type:id,name',
                'currentAssignment.assignedTo:staff_id,firstname,lastname,dept_id',
                'currentAssignment.assignedTo.department:id,name',
                'currentAssignment.deliveryDocument',
                'currentAssignment.returnDocument',
                'assignments',
                // 'assignments.assignedTo:staff_id,firstname,lastname',
                'assignments.deliveryDocument',
                'assignments.returnDocument',
                'assignments.assignedTo:staff_id,firstname,lastname',
                'histories',
                'histories.performer:staff_id,firstname,lastname',
                'histories.deliveryRecord:id,file_path',

            )->
                when($filtersDto->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('brand', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%")
                            ->orWhere('serial_number', 'like', "%{$search}%")
                            ->orWhereHas('currentAssignment.assignedTo', function ($q2) use ($search) {
                                $q2->where('firstname', 'like', "%{$search}%")
                                    ->orWhere('lastname', 'like', "%{$search}%")->orWhere(DB::raw("CONCAT(firstname, ' ', lastname)"), 'like', "%{$search}%");
                                // $q2->orWhere(DB::raw("CONCAT(firstname, ' ', lastname)"), 'like', "%{$search}%");
        
                            });
                    });
                })->when($filtersDto->status && count($filtersDto->status) > 0, function ($query) use ($filtersDto) {
                    $query->whereIn('status', $filtersDto->status);
                })->when($filtersDto->types, function ($query, $typeId) {
                    $query->where('type_id', $typeId);
                })->when($filtersDto->assigned_to, function ($query) use ($filtersDto) {
                    $ids = array_filter($filtersDto->assigned_to);

                    $query->where(function ($q) use ($ids, $filtersDto) {
                        if ($ids) {
                            $q->whereHas(
                                'currentAssignment',
                                fn($q2) =>
                                $q2->whereIn('assigned_to_id', $ids)
                            );
                        }

                        if (in_array(null, $filtersDto->assigned_to, true)) {
                            $q->orWhereDoesntHave('currentAssignment');
                        }
                    });
                })
                ->when($filtersDto->department_id && count($filtersDto->department_id) > 0, function ($query) use ($filtersDto) {
                    $query->where(function ($q) use ($filtersDto) {
                        $q->whereHas('currentAssignment.assignedTo', function ($q2) use ($filtersDto) {
                            $q2->whereIn('dept_id', $filtersDto->department_id);
                        });
                        // $q->orWhereDoesntHave('currentAssignment');
                    });
                })->
                latest()->paginate(10)->withQueryString();
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al obtener los activos');
        }
    }

    public function getDetails(Asset $asset)
    {
        return $asset->load(
            'type:id,name',

            'assignments:id,asset_id,assigned_to_id,assigned_at,returned_at',
            'assignments.assignedTo:staff_id,firstname,lastname,dept_id',

            'currentAssignment.assignedTo:staff_id,firstname,lastname,dept_id',
            'currentAssignment.assignedTo.department:id,name',
            'currentAssignment.deliveryDocument',
            'currentAssignment.returnDocument',
        );
    }

    public function getHistories(Asset $asset)
    {
        try {
            return $asset->load(
                'histories.performer:staff_id,firstname,lastname',
                'histories.deliveryRecord:id,file_path',
            );
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al obtener el historial del activo');
        }
    }





    public function getStats()
    {

        try {
            $totalAssets = Asset::count();

            $assignedAssets = Asset::where('status', AssetStatus::ASSIGNED->value)->count();
            $availableAssets = Asset::where('status', AssetStatus::AVAILABLE->value)->count();
            $inRepairAssets = Asset::where('status', AssetStatus::IN_REPAIR->value)->count();
            $decommissionedAssets = Asset::where('status', AssetStatus::DECOMMISSIONED->value)->count();

            $notExpiredWarrantyAssets = Asset::whereDate('warranty_expiration', '>=', now()->toDateString())->count();
            $expiredWarrantyAssets = Asset::whereDate('warranty_expiration', '<', now()->toDateString())->count();

            return [
                'total' => $totalAssets,
                'statuses' => [
                    AssetStatus::ASSIGNED->value => $assignedAssets,
                    AssetStatus::AVAILABLE->value => $availableAssets,
                    AssetStatus::IN_REPAIR->value => $inRepairAssets,
                    AssetStatus::DECOMMISSIONED->value => $decommissionedAssets,
                ],
                'not_expired_warranty' => $notExpiredWarrantyAssets,
                'expired_warranty' => $expiredWarrantyAssets,
            ];
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al obtener las estadísticas de activos');
        }
    }


    public function storeAsset(StoreAssetDto $dto)
    {
        try {
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
                    'action' => AssetHistoryAction::CREATED->value,
                    'description' => 'Equipo registrado en el sistema',
                    'asset_id' => $asset->id,
                    'performed_by' => auth()->user()->staff_id,
                    'performed_at' => now(),
                ]);

                return $asset;
            });


            return $asset;
        } catch (\Exception $e) {

            throw new InternalErrorException('Error al registrar el activo');
        }

    }

    public function updateAsset(UpdateAssetDto $dto, Asset $asset)
    {
        try {

            $assetUpdated = DB::transaction(function () use ($dto, $asset) {
                $description = $this->fieldChangesToDescription($dto, $asset);

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


                AssetHistory::create([
                    'action' => AssetHistoryAction::UPDATED->value,
                    'description' => $description,
                    'asset_id' => $asset->id,
                    'performed_by' => auth()->user()->staff_id,
                    'performed_at' => now(),
                ]);

                return $asset;
            });

            return $assetUpdated;
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al actualizar el activo');
        }
    }




    private function fieldChangesToDescription(UpdateAssetDto $dto, Asset $asset): string
    {
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
                    $fieldChanges[] = $this->messageChange($key, $assetValue, $value);
                }
                continue;
            }


            if ($key === 'is_new') {
                $newValueLabel = $value ? 'Sí' : 'No';
                $assetValueLabel = $asset->$key ? 'Sí' : 'No';
                if ($value !== null && $asset->$key != $value) {
                    $fieldChanges[] = $this->messageChange($key, $assetValueLabel, $newValueLabel);
                }
                continue;
            }

            if ($key === 'type_id') {
                $newType = AssetType::find($value);
                $oldType = AssetType::find($asset->$key);
                $newTypeName = $newType ? $newType->name : 'N/A';
                $oldTypeName = $oldType ? $oldType->name : 'N/A';
                if ($value !== null && $asset->$key != $value) {
                    $fieldChanges[] = $this->messageChange($key, $oldTypeName, $newTypeName);
                }
                continue;
            }

            if ($value !== null && $asset->$key != $value) {
                $fieldChanges[] = $this->messageChange($key, $asset->$key, $value);
            }
        }



        return implode(', ', $fieldChanges);
    }

    private function messageChange($key, $old, $new)
    {
        if (empty($old)) {
            return "{$this->fromKeyToLabel($key)} establecido a '{$new}'";
        }
        return "{$this->fromKeyToLabel($key)} cambiado de '{$old}' a '{$new}'";
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


    public function changeAssetStatus(Asset $asset, string $newStatus)
    {
        // $asset = Asset::find($assetId);
        // if (!$asset) {
        //     throw new NotFoundHttpException('No se encontró el activo');
        // }
        try {

            if ($asset->status === $newStatus) {
                throw new BadRequestException('El activo ya tiene el estado proporcionado.');
            }

            if ($newStatus === AssetStatus::ASSIGNED->value) {
                throw new BadRequestException('No se puede cambiar el estado a ASIGNADO directamente. Use la función de asignación.');
            }

            if ($asset->currentAssignment) {
                throw new BadRequestException('No se puede cambiar el estado de un activo asignado. Primero debe devolverlo.');
            }

            $oldStatus = $asset->status;

            DB::transaction(function () use ($asset, $newStatus, $oldStatus) {
                $asset->update([
                    'status' => $newStatus,
                ]);

                AssetHistory::create([
                    'action' => AssetHistoryAction::STATUS_CHANGED->value,
                    'description' => "Estado cambiado de '" . $this->fromStatusToLabel($oldStatus) . "' a '" . $this->fromStatusToLabel($newStatus) . "'",
                    'asset_id' => $asset->id,
                    'performed_by' => auth()->user()->staff_id,
                    'performed_at' => now(),
                ]);
            });

        } catch (\Exception $e) {
            if ($e instanceof BadRequestException) {
                throw $e;
            }

            throw new InternalErrorException('Error al cambiar el estado del activo');
        }


    }


    public function assignAsset(AssignAssetDto $dto)
    {

        try {
            DB::transaction(function () use ($dto) {


                $asset = Asset::find($dto->asset_id);
                if (!$asset) {
                    throw new NotFoundHttpException('No se encontró el activo');
                }
                if ($asset->status !== AssetStatus::AVAILABLE->value) {
                    throw new BadRequestException('Solo se pueden asignar equipos disponibles.');
                }

                $assignment = $asset->currentAssignment;

                // 1️⃣ Si está asignado a otro usuario → bloquear
                if ($assignment && $assignment->assigned_to_id !== $dto->assigned_to_id) {
                    throw new BadRequestException(
                        'El activo ya está asignado a un usuario, debe devolverlo antes de reasignarlo.'
                    );
                }

                // 2️⃣ Si existe asignación activa → posible actualización
                if ($assignment) {

                    // Guardamos valores originales
                    $original = $assignment->getOriginal();

                    // Aplicamos cambios
                    $assignment->fill([
                        'comment' => $dto->comment,
                        'assigned_at' => Carbon::parse($dto->assign_date)->startOfDay(),
                    ]);

                    // 3️⃣ Si no hay cambios reales
                    if (!$assignment->isDirty()) {
                        throw new BadRequestException('No se detectaron cambios en la asignación.');
                    }

                    // Guardar
                    $assignment->save();

                    // 4️⃣ Construir descripción de forma correcta
                    $changes = [];

                    if ($assignment->wasChanged('comment')) {
                        $changes[] = "Comentario actualizado a: {$assignment->comment}";
                    }

                    if ($assignment->wasChanged('assigned_at')) {
                        $changes[] = "Fecha de asignación actualizada a: " .
                            $assignment->assigned_at->format('d/m/Y');
                    }

                    $description = implode('. ', $changes);

                    // 5️⃣ Historial
                    AssetHistory::create([
                        'action' => AssetHistoryAction::ASSIGNED->value,
                        'description' => $description . " para {$assignment->assignedTo->full_name}",
                        'asset_id' => $asset->id,
                        'performed_by' => auth()->user()->staff_id,
                        'performed_at' => now(),
                        'related_assignment_id' => $assignment->id,
                    ]);

                    return;
                }


                $asset->update([
                    'status' => AssetStatus::ASSIGNED->value,
                ]);

                $assigned = AssetAssignment::create(
                    [
                        'assigned_to_id' => $dto->assigned_to_id,
                        'assigned_at' => $dto->assign_date,
                        'comment' => $dto->comment,
                        'asset_id' => $dto->asset_id,
                    ]
                );


                AssetHistory::create([

                    'action' => AssetHistoryAction::ASSIGNED->value,
                    'description' => "Equipo asignado a {$assigned->assignedTo->full_name}",
                    'asset_id' => $dto->asset_id,
                    'performed_by' => auth()->user()->staff_id,
                    'performed_at' => now(),
                    'related_assignment_id' => $assigned->id,
                ]);







            });
        } catch (\Exception $e) {
            if ($e instanceof BadRequestException || $e instanceof NotFoundHttpException) {
                throw $e;
            }

            throw new InternalErrorException('Error al asignar el activo');
        }
    }

    public function devolveAsset(DevolveAssetDto $dto)
    {
        try {
            DB::transaction(function () use ($dto) {
                if ($dto->assignment->returned_at) {
                    throw new BadRequestException('La asignación ya ha sido devuelta.');
                }

                $responsible = User::find($dto->responsible_id);
                if (!$responsible) {
                    throw new NotFoundHttpException('No se encontró el usuario responsable.');
                }

                if ($responsible->dept_id !== Allowed::SYSTEM_TI->value) {
                    throw new BadRequestException('El usuario responsable debe pertenecer al departamento de SISTEMAS / TI.');
                }

                DB::transaction(function () use ($dto) {

                    AssetAssignment::where('id', $dto->assignment->id)->update([
                        'returned_at' => Carbon::parse($dto->return_date)->toDateTimeString(),
                        'return_comment' => $dto->return_comment,
                        'responsible_id' => $dto->responsible_id,
                        'return_reason' => $dto->return_reason,
                    ]);

                    Asset::where('id', $dto->assignment->asset_id)->update([
                        'status' => AssetStatus::AVAILABLE->value,
                    ]);

                    AssetHistory::create([
                        'action' => AssetHistoryAction::RETURNED->value,
                        'description' => "Equipo devuelto por {$dto->assignment->assignedTo->full_name} por motivo: " . ReturnReason::labels(ReturnReason::from($dto->return_reason)),
                        'asset_id' => $dto->assignment->asset_id,
                        'performed_by' => auth()->user()->staff_id,
                        'performed_at' => now(),
                        'related_assignment_id' => $dto->assignment->id,
                    ]);

                });

            });
        } catch (\Exception $e) {
            if ($e instanceof BadRequestException || $e instanceof NotFoundHttpException) {
                throw $e;
            }

            throw new InternalErrorException('Error al devolver el activo');
        }
    }


    public function generateLaptopAssignmentDocument(
        AssetAssignment $assignment
    ) {
        try {

            // $assignment->load('assignedTo');
            if (!$assignment->assignedTo) {
                throw new BadRequestException('El activo no está asignado a ningún usuario');
            }
            $asset = $assignment->asset;


            Storage::disk('public')->makeDirectory('documents');


            $template = new TemplateProcessor(storage_path('app/templates/cargo-laptop.docx'));


            $template->setValue('type', strtoupper($asset->type->name));
            $template->setValue('assign_date', $assignment->assigned_at->translatedFormat('d \d\e F \d\e\l Y'));
            $template->setValue('fullname', strtoupper($assignment->assignedTo->full_name));
            $template->setValue('dni', $assignment->assignedTo->dni ?? 'N/A');
            $template->setValue('is_new', $asset->is_new ? 'NUEVO' : 'USADO');
            $template->setValue('brand', strtoupper($asset->brand));
            $template->setValue('model', strtoupper($asset->model));
            $template->setValue('serial_number', $asset->serial_number);
            $template->setValue('processor', strtoupper($asset->processor ?? 'N/A'));
            $template->setValue('ram', strtoupper($asset->ram ?? 'N/A'));
            $template->setValue('storage', strtoupper($asset->storage ?? 'N/A'));

            $template->setValue('comment', $assignment->comment ?? '');


            $fileName = 'cargo_laptop_' . strtolower(str_replace(' ', '_', $assignment->assignedTo->full_name)) . '_' . Carbon::now()->format('Ymd_His') . '.docx';
            $path = storage_path('app/public/documents/' . $fileName);
            $template->saveAs($path);

            return $path;
        } catch (\Exception $e) {
            if ($e instanceof BadRequestException) {
                throw $e;
            }

            throw new InternalErrorException('Error al generar el documento de asignación de laptop');
        }
    }

    public function generateCellphoneAssignmentDocument(
        AssetAssignment $assignment
    ) {
        try {
            if (!$assignment->assignedTo) {
                throw new BadRequestException('El activo no está asignado a ningún usuario');
            }

            $asset = $assignment->asset;

            Storage::disk('public')->makeDirectory('documents');

            $template = new TemplateProcessor(storage_path('app/templates/cargo-celular.docx'));
            // 24/11/2025
            $template->setValue('assign_date', $assignment->assigned_at->format('d/m/Y'));
            $template->setValue('fullname', strtoupper($assignment->assignedTo->full_name));
            $template->setValue('dni', $assignment->assignedTo->dni ?? 'N/A');
            $template->setValue('department', strtoupper($assignment->assignedTo->department->name ?? 'N/A'));
            $template->setValue('is_new', $asset->is_new ? 'NUEVO' : 'USADO EN BUEN ESTADO');
            $template->setValue('brand', strtoupper($asset->brand));
            $template->setValue('model', strtoupper($asset->model));
            $template->setValue('comment', $assignment->comment ?? 'N/A');
            $template->setValue('phone', $asset->phone ?? 'N/A');
            $template->setValue('imei', $asset->imei ?? 'N/A');

            $fileName = "cargo_{$asset->type->name}_" . strtolower(str_replace(' ', '_', $assignment->assignedTo->full_name)) . '_' . Carbon::now()->format('Ymd_His') . '.docx';
            $path = storage_path('app/public/documents/' . $fileName);

            $template->saveAs($path);

            return $path;
        } catch (\Exception $e) {
            if ($e instanceof BadRequestException) {
                throw $e;
            }

            throw new InternalErrorException('Error al generar el documento de asignación de celular');
        }

    }


    public function generateReturnDocument(AssetAssignment $assignment)
    {
        try {

            if (!$assignment->assignedTo) {
                throw new BadRequestException('El activo no está asignado a ningún usuario');
            }

            $asset = $assignment->asset;

            Storage::disk('public')->makeDirectory('documents');

            $template = new TemplateProcessor(storage_path('app/templates/devolucion-equipo.docx'));

            $hour = $assignment->returned_at->format('H:i');
            $dayMonth = $assignment->returned_at->translatedFormat('d \d\e F');
            $year = $assignment->returned_at->translatedFormat('Y');

            $template->setValue('type', strtoupper($asset->type->name));
            $template->setValue('hour', $hour);
            $template->setValue('day_month', $dayMonth);
            $template->setValue('year', $year);
            $template->setValue('fullname', strtoupper($assignment->assignedTo->full_name));
            $template->setValue('dni', $assignment->assignedTo->dni ?? 'N/A');
            $template->setValue('charge', $assignment->assignedTo->charge->descripcion_cargo ?? 'N/A');
            $template->setValue('is_termination', $assignment->return_reason === ReturnReason::TERMINATION_EMPLOYMENT->value ? 'X' : '');
            $template->setValue('is_technical', $assignment->return_reason === ReturnReason::TECHNICAL_ISSUES->value ? 'X' : '');
            $template->setValue('is_renovation', $assignment->return_reason === ReturnReason::EQUIPMENT_RENOVATION->value ? 'X' : '');
            $template->setValue('brand', strtoupper($asset->brand));
            $template->setValue('model', strtoupper($asset->model));
            $template->setValue('color', strtoupper($asset->color));
            $template->setValue('serial_number', $asset->serial_number);
            $template->setValue('comments', $assignment->return_comment ?? '');
            $template->setValue('responsible', strtoupper($assignment->responsible->full_name ?? 'N/A'));

            $fileName = 'devolucion_equipo_' . strtolower(str_replace(' ', '_', $assignment->assignedTo->full_name)) . '_' . Carbon::now()->format('Ymd_His') . '.docx';
            $path = storage_path('app/public/documents/' . $fileName);
            $template->saveAs($path);

            return $path;
        } catch (\Exception $e) {
            if ($e instanceof BadRequestException) {
                throw $e;
            }

            throw new InternalErrorException('Error al generar el documento de devolución del activo');
        }
    }

    public function uploadDeliveryRecord(UploadDeliveryRecordDto $dto)
    {
        try {
            if (!$dto->assignment) {
                throw new NotFoundHttpException('No se encontró la asignación del activo');
            }

            $recordType = $dto->type === DeliveryRecordType::ASSIGNMENT->value ? 'asignación' : 'devolución';
            $description = "Constancia de {$recordType} subida para '{$dto->assignment->assignedTo->full_name}'";

            if ($dto->assignment->deliveryRecords()->where('type', $dto->type)->exists()) {
                $description = "Constancia de {$recordType} actualizada para '{$dto->assignment->assignedTo->full_name}'";
            }
            // $path = '';
            $path = Storage::disk('public')->putFile('delivery_records', $dto->file);

            DB::transaction(function () use ($dto, $path, $description) {

                $deliveryRecord = DeliveryRecord::create([
                    'file_path' => $path,
                    'assignment_id' => $dto->assignment->id,
                    'type' => $dto->type,
                ]);

                $dto->assignment->deliveryRecords()->save($deliveryRecord);

                AssetHistory::create([
                    'action' => AssetHistoryAction::DELIVERY_RECORD_UPLOADED->value,
                    'description' => $description,
                    'asset_id' => $dto->assignment->asset_id,
                    'performed_by' => auth()->user()->staff_id,
                    'performed_at' => now(),
                    'related_delivery_record_id' => $deliveryRecord->id,
                ]);
            });



            return Storage::disk('public')->url($path);
        } catch (\Exception $e) {
            if ($e instanceof NotFoundHttpException) {
                throw $e;
            }

            throw new InternalErrorException('Error al subir la constancia de entrega');
        }
    }

    public function uploadInvoiceDocument(Asset $asset, string $file)
    {

        try {
            $filePath = Storage::disk('public')->putFile('invoices', $file);


            $description = "Documento de factura subido correctamente.";

            if ($asset->invoice_url) {
                $description = "Documento de factura actualizado correctamente.";
            }

            DB::transaction(function () use ($asset, $filePath, $description) {

                $asset->update([
                    'invoice_path' => $filePath,
                ]);

                AssetHistory::create([
                    'action' => AssetHistoryAction::INVOICE_UPLOADED->value,
                    'description' => $description,
                    'asset_id' => $asset->id,
                    'performed_by' => auth()->user()->staff_id,
                    'performed_at' => now(),
                    'invoice_path' => $filePath,

                ]);
            });

            return Storage::disk('public')->url($filePath);

        } catch (\Exception $e) {
            throw new InternalErrorException('Error al subir el documento de factura');
        }
    }


}