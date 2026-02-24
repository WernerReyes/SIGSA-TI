<?php
namespace App\Services;

use App\DTOs\Asset\AssetFiltersDto;
use App\DTOs\Asset\AssetHistoryFiltersDto;
use App\DTOs\Asset\AssignAssetDto;
use App\DTOs\Asset\DevolveAssetDto;
use App\DTOs\Asset\StoreAssetDto;
use App\DTOs\Asset\UpdateAssetDto;
use App\DTOs\Asset\UpdateStatusDto;
use App\DTOs\Asset\UploadDeliveryRecordDto;
use App\Enums\Alert\AlertType;
use App\Enums\Alert\EntityType;
use App\Enums\Asset\AssetStatus;
use App\Enums\Asset\AssetTypeCategory;
use App\Enums\Asset\AssetTypeEnum;
use App\Enums\AssetAssignment\ReturnReason;
use App\Enums\AssetHistory\AssetHistoryAction;
use App\Enums\Company\EmployeeCompany;
use App\Enums\DeliveryRecord\DeliveryRecordType;
use App\Enums\Department\Allowed;
use App\Models\Alert;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetHistory;
use App\Models\AssetReparation;
use App\Models\AssetType;

use App\Utils\CompressImage;
use Intervention\Image\Facades\Image;
use App\Models\DeliveryRecord;
use App\Models\User;
use App\Models\Ticket;

use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class AssetService
{
    private function isFromRRHH()
    {
        return auth()->user()->dept_id == Allowed::RRHH->value;
    }
    public function getPaginated(AssetFiltersDto $filtersDto)
    {
        try {

            return Asset::query()
                ->with([
                    'type:id,name,doc_category',
                    'currentAssignment:id,asset_id,assigned_to_id,assigned_at,parent_assignment_id,created_at',
                    'currentAssignment.assignedTo:staff_id,firstname,lastname,dept_id',
                    'currentAssignment.assignedTo.department:id,name',
                    'currentAssignment.childrenAssignments:id,asset_id,assigned_to_id,parent_assignment_id',
                    'currentAssignment.childrenAssignments.asset.type:id,name,doc_category',
                    'currentAssignment.childrenAssignments.asset.currentAssignment:id,asset_id,assigned_to_id,assigned_at,parent_assignment_id',
                    'currentAssignment.childrenAssignments.asset.currentAssignment.assignedTo:staff_id,firstname,lastname,dept_id',
                    'currentAssignment.childrenAssignments.asset.currentAssignment.assignedTo.department:id,name',
                ])->

                /*
                |--------------------------------------------------------------------------
                | Activos raíz (no accesorios huérfanos)
                |--------------------------------------------------------------------------
                */
                isFromRRHH()
                ->filters($filtersDto)
                ->latest()
                ->orderByDesc('id')
                ->paginate(10)
                ->withQueryString();

        } catch (\Throwable $e) {
            throw new InternalErrorException('Error al obtener los activos' . $e->getMessage());
        }
    }


    public function getAvailableAssets()
    {
        try {
            return Asset::
                select('id', 'name', 'model', 'brand', 'status', 'type_id')
                ->with('type:id,name,doc_category')
                ->where('status', AssetStatus::AVAILABLE->value)->get();
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al obtener los equipos disponibles');
        }
    }

    public function getAccessories($asset_id = null)
    {
        $asset = null;
        if ($asset_id) {
            $asset = Asset::select('id', 'brand', 'type_id')
                ->with('type:id,name,doc_category')
                ->find($asset_id);
            if (!$asset) {
                throw new NotFoundHttpException('Activo no encontrado');
            }
        }
        try {
            return Asset::query()
                ->select('id', 'name', 'model', 'brand', 'serial_number', 'status', 'type_id')
                ->with('type:id,name,doc_category')
                ->isFromRRHH()
                ->where('status', AssetStatus::AVAILABLE->value)

                // Siempre accesorios
                ->whereHas('type', function ($q) {
                    $q->where('doc_category', AssetTypeCategory::ACCESSORY->value);
                })

                // 👇 Solo aplica la marca si el tipo es cargador
                ->where(function ($query) use ($asset) {

                    // Cargadores → filtrar por marca
                    $query->whereHas('type', function ($q) use ($asset) {
                        // $chargerFor = $asset->type->name === 'Celular' ? 'Cargador de Celular' : ($asset->type->name === 'Laptop' ? 'Cargador de Laptop' : null);
                        $chargerFor = match ($asset?->type->id) {
                            AssetTypeEnum::CELL_PHONE => AssetTypeEnum::CELL_PHONE_CHARGER,
                            AssetTypeEnum::LAPTOP => AssetTypeEnum::LAPTOP_CHARGER,
                            default => null,
                        };
                        $q->where('id', $chargerFor)
                            ->when($asset?->brand, fn($qq) => $qq->whereNotNull('id'));

                    })->when($asset?->brand, function ($q) use ($asset) {
                        $q->where('brand', $asset->brand);
                    });

                    // Otros accesorios → sin filtro por marca
                    $query->orWhereHas('type', function ($q) {
                        $q->where('doc_category', AssetTypeCategory::ACCESSORY->value)
                            ->where('name', 'not like', '%cargador%');
                    });
                })

                ->get();
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al obtener los accesorios');
        }

    }

    public function getDetails(Asset $asset)
    {

        return $asset->load(
            'type:id,name,doc_category',

            'assignments:id,asset_id,assigned_to_id,assigned_at,returned_at,parent_assignment_id',
            // 'assignments.asset:id,type_id',
            // 'assignments.asset.type:id,name,doc_category',
            'assignments.assignedTo:staff_id,firstname,lastname,dept_id',
            'assignments.deliveryDocument',
            'assignments.returnDocument',

            'assignments.parentAssignment:id,asset_id,assigned_to_id,assigned_at,returned_at,parent_assignment_id',
            'assignments.parentAssignment.asset:id,name,brand,model,serial_number,type_id',
            'assignments.parentAssignment.asset.type:id,name,doc_category',
            'assignments.parentAssignment.deliveryDocument',
            'assignments.parentAssignment.returnDocument',

            'assignments.childrenAssignments:id,asset_id,assigned_to_id,parent_assignment_id',
            'assignments.childrenAssignments.asset:id,name,brand,model,serial_number,type_id',
            'assignments.childrenAssignments.asset.type:id,name',

            'reparations:id,asset_id,date,description,image_paths',

            'currentAssignment.assignedTo:staff_id,firstname,lastname,dept_id',
            'currentAssignment.assignedTo.department:id,name',

        );
    }


    public function getAssignmentsByUser(int $user_id)
    {
        try {
            return AssetAssignment::query()
                ->with('asset:id,name,brand,model,serial_number,type_id', 'asset.type:id,name,doc_category', 'childrenAssignments:id,asset_id,assigned_to_id,parent_assignment_id', 'childrenAssignments.asset:id,name,brand,model,serial_number,type_id', 'childrenAssignments.asset.type:id,name,doc_category')
                ->where('assigned_to_id', $user_id)
                // ->whereNull('returned_at')
                ->where('parent_assignment_id', null)
                ->orderBy('assigned_at', 'desc')->get();
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al obtener las asignaciones del usuario');
        }
    }


    public function getHistoriesPaginated(Asset $asset, AssetHistoryFiltersDto $filtersDto)
    {
        try {
            return $asset->histories()
                ->with('performer:staff_id,firstname,lastname', 'deliveryRecord:id,file_path')
                ->when($filtersDto->actions && count($filtersDto->actions) > 0, function ($query) use ($filtersDto) {
                    $query->whereIn('action', $filtersDto->actions);
                })
                ->when($filtersDto->start_date, function ($query) use ($filtersDto) {
                    $query->whereDate('performed_at', '>=', $filtersDto->start_date);
                })
                ->when($filtersDto->end_date, function ($query) use ($filtersDto) {
                    $query->whereDate('performed_at', '<=', $filtersDto->end_date);
                })
                ->latest('performed_at')
                ->
                paginate(10, ['*'], 'page_histories')->withQueryString();
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al obtener el historial del activo');
        }
    }
    public function getAssignDocument(AssetAssignment $assignment)
    {
        try {
            return $assignment->load('deliveryDocument');
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al obtener el documento de entrega');
        }
    }

    public function getStats(AssetFiltersDto $filtersDto)
    {

        try {
            $totalAssets = Asset::isFromRRHH()->filters($filtersDto, true)->count();

            $statuses = Asset::isFromRRHH()
                ->selectRaw('status, COUNT(*) as count')
                ->filters($filtersDto, true)
                ->groupBy('status')
                ->pluck('count', 'status');


            $notExpiredWarrantyAssets = Asset::isFromRRHH()->filters($filtersDto, true)->whereDate('warranty_expiration', '>=', now()->toDateString())->count();
            $expiredWarrantyAssets = Asset::isFromRRHH()->filters($filtersDto, true)->whereDate('warranty_expiration', '<', now()->toDateString())->count();

            return [
                'total' => $totalAssets,
                'statuses' => $statuses,
                'not_expired_warranty' => $notExpiredWarrantyAssets,
                'expired_warranty' => $expiredWarrantyAssets,
            ];
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al obtener las estadísticas de activos');
        }
    }

    public function getAccessoriesOutOfStockAlert()
    {
        try {
            return Alert::where('entity', EntityType::ASSET->value)
                ->where('type', AlertType::ACCESSORY_OUT_OF_STOCK->value)
                ->first();
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al obtener las alertas de accesorios agotados');
        }
    }

    public function resendAccessoryOutOfStockAlert()
    {
        if ($this->isFromRRHH()) {
            throw new BadRequestException('No tienes permiso para reenviar esta alerta');
        }

        if (!Asset::whereHas('type', fn($q) => $q->where('doc_category', AssetTypeCategory::ACCESSORY->value))->whereNot('status', AssetStatus::AVAILABLE->value)->exists()) {
            throw new BadRequestException('No hay accesorios agotados actualmente, no se puede reenviar la alerta');
        }

        try {
            $alertService = app(AccessoryOutOfStockAlertService::class);
            $alertService->forceNotify();
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al reenviar la alerta de accesorios agotados');
        }
    }

    private function logHistory(Asset $asset, AssetHistoryAction $action, string $description, ?int $deliveryRecordId = null)
    {
        AssetHistory::create([
            'action' => $action->value,
            'description' => $description,
            'asset_id' => $asset->id,
            'performed_by' => auth()->user()->staff_id,
            'delivery_record_id' => $deliveryRecordId,
            // 'performed_at' => now()->utc(),
        ]);
    }


    public function storeAsset(StoreAssetDto $dto)
    {


        if ($this->isFromRRHH()) {
            if (!in_array($dto->type_id, AssetTypeEnum::RRHHTypes())) {
                throw new BadRequestException('Los usuarios de RRHH solo pueden registrar activos de tipo Celular o Accesorio');
            }
        }


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

                $this->logHistory($asset, AssetHistoryAction::CREATED, 'Equipo registrado en el sistema');



                return $asset;
            });


            return $asset;
        } catch (\Exception $e) {
            if ($e->getCode() === '23000') {
                throw new BadRequestException('El número de serie o IMEI ya existe en el sistema.');
            }

            throw new InternalErrorException('Error al registrar el activo');
        }

    }

    public function updateAsset(UpdateAssetDto $dto, Asset $asset)
    {

        if ($this->isFromRRHH() && $dto->type_id) {
            $type = AssetType::find($dto->type_id);
            if ($type && !in_array($type->name, ['Celular', 'Cargador de Celular'])) {
                throw new BadRequestException('Los usuarios de RRHH solo pueden registrar activos de tipo Celular o Cargador de Celular');
            }
        }

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
                    // 'performed_at' => now()->utc(),
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


    public function deleteAsset(Asset $asset)
    {
        if ($asset->status !== AssetStatus::AVAILABLE->value) {
            throw new BadRequestException('Solo se pueden eliminar equipos disponibles');
        }

        try {
            $asset->delete();
        } catch (\Exception $e) {
            if ($e->getCode() === '23000') {
                throw new BadRequestException('No se puede eliminar el activo porque tiene relaciones con otros activos, asignaciones o reparaciones. Por favor, elimine primero esas relaciones.');
            }
            throw new InternalErrorException('Error al eliminar el activo: ' . $e->getMessage());
        }
    }


    public function changeAssetStatus(Asset $asset, UpdateStatusDto $dto)
    {
        try {

            if ($asset->status === $dto->status) {
                throw new BadRequestException('El activo ya tiene el estado proporcionado.');
            }

            if ($dto->status === AssetStatus::ASSIGNED->value && !$asset->currentAssignment) {
                throw new BadRequestException('No se puede cambiar el estado a ASIGNADO directamente. Use la función de asignación.');
            }

            if (array_search($dto->status, [AssetStatus::DECOMMISSIONED->value, AssetStatus::AVAILABLE->value]) && $asset->currentAssignment) {
                throw new BadRequestException('No se puede cambiar el estado de un activo asignado. Primero debe devolverlo.');
            }

            $oldStatus = $asset->status;

            DB::transaction(function () use ($asset, $oldStatus, $dto) {
                $asset->update([
                    'status' => $dto->status,
                    'notes' => AssetStatus::DECOMMISSIONED->value ? $dto->description : null,
                ]);

                $description = "Estado cambiado de '" . $this->fromStatusToLabel($oldStatus) . "' a '" . $this->fromStatusToLabel($dto->status) . "'";


                if ($dto->description) {
                    $description .= " por motivo de '" . $dto->description . "'";
                }

                if ($dto->status === AssetStatus::IN_REPAIR->value) {

                    $images = [];
                    if ($dto->images) {
                        foreach ($dto->images as $image) {
                            $path = CompressImage::save($image, 'asset_reparations');
                            $images[] = $path;
                        }
                    }

                    AssetReparation::create([
                        'asset_id' => $asset->id,
                        'date' => Carbon::parse($dto->date)->startOfDay(),
                        'description' => $dto->description,
                        'image_paths' => $images,
                    ]);

                    $stringImage = count($images) > 0 ? implode(',', $images) : '';
                    $description .= " con fecha de inicio '" . date('Y-m-d', strtotime($dto->date)) . "'";
                    if ($stringImage) {
                        $description .= "'" . $stringImage . "'";
                    }
                }

                AssetHistory::create([
                    'action' => AssetHistoryAction::STATUS_CHANGED->value,
                    'description' => $description,
                    'asset_id' => $asset->id,
                    'performed_by' => auth()->user()->staff_id,
                    // 'performed_at' => now()->utc(),
                ]);

                app(AccessoryOutOfStockAlertService::class)->check();
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

        if ($dto->ticket_id) {
            $userAuthID = auth()->user()->staff_id;
            $ticket = Ticket::select(
                'id',
                'responsible_id'
            )->find($dto->ticket_id);
            if ($ticket->responsible_id !== $userAuthID) {
                throw new BadRequestException('Solo el responsable del ticket puede asignar activos al mismo.');
            }
        }

        try {
            $assignment = DB::transaction(function () use ($dto) {


                $asset = Asset::find($dto->asset_id);
                if (!$asset) {
                    throw new NotFoundHttpException('No se encontró el activo');
                }

                if (in_array($asset->status, [AssetStatus::IN_REPAIR->value, AssetStatus::DECOMMISSIONED->value])) {
                    throw new BadRequestException('Solo se pueden asignar equipos disponibles.');
                }

                $assignment = $asset->currentAssignment;


                // 2️⃣ Si existe asignación activa → posible actualización
                if ($assignment) {
                    if (!$assignment->canBeEdited()) {
                        throw new BadRequestException('No es posible editar esta asignación, ha pasado el tiempo límite de edición.');
                    }
                    // Guardamos valores originales
                    $original = $assignment->getOriginal();

                    // Aplicamos cambios
                    $assignment->fill([
                        'comment' => $dto->comment,
                        'assigned_at' => Carbon::parse($dto->assign_date)->startOfDay(),
                        'assigned_to_id' => $dto->assigned_to_id,
                    ]);

                    $accessoriesChanged = false;
                    $assetsIds = array_map(
                        fn($a) => $a['asset_id'],
                        $assignment->childrenAssignments->select('id', 'asset_id')->toArray()
                    );
                    if (
                        $assetsIds !== $dto->accessories
                    ) {
                        $accessoriesChanged = true;
                    }


                    // 3️⃣ Si no hay cambios reales
                    if (!$assignment->isDirty() && !$accessoriesChanged) {
                        throw new BadRequestException('No se detectaron cambios en la asignación.');
                    }

                    // Guardar
                    $assignment->save();

                    // 4️⃣ Construir descripción de forma correcta
                    $changes = [];

                    if ($assignment->wasChanged('comment')) {
                        $changes[] = "Comentario actualizado de '{$original['comment']}' a '{$assignment->comment}'";
                    }

                    if ($assignment->wasChanged('assigned_to_id')) {
                        $changes[] = "Asignación cambiada de {$assignment->assignedTo->full_name} a " .
                            User::find($original['assigned_to_id'])->full_name;
                    }


                    if ($accessoriesChanged) {
                        $assetsToReleaseIds = array_diff($assetsIds, $dto->accessories ?? []);
                        $assets = Asset::whereIn('id', $assetsToReleaseIds)
                            ->select('id', 'name', 'brand', 'model', 'serial_number') // Incluir todas las columnas que usa getFullNameAttribute
                            ->get();


                        $changes[] =
                            (count($assetsToReleaseIds) > 0 ?
                                implode(',', $assets->pluck('full_name')->map(fn($name) => "{$name} liberado")->toArray()) : 'No hay accesorios liberados')
                            . "," .
                            (count($dto->accessories ?? []) > 0 ?
                                implode(',', Asset::whereIn('id', $dto->accessories ?? [])
                                    ->select('id', 'name', 'brand', 'model', 'serial_number') // Incluir todas las columnas que usa getFullNameAttribute
                                    ->get()
                                    ->pluck('full_name')
                                    ->map(fn($name) => "{$name} asignado")
                                    ->toArray()) : 'No hay accesorios asignados');



                        if (count($assetsToReleaseIds) > 0) {
                            // Liberar accesorios antiguos
                            Asset::whereIn('id', $assetsToReleaseIds)->update([
                                'status' => AssetStatus::AVAILABLE->value,
                            ]);
                            $assignment->childrenAssignments()->whereIn('asset_id', $assetsToReleaseIds)->delete();
                        }




                        // Asignar accesorios nuevos si los hay
                        if ($dto->accessories && is_array($dto->accessories)) {
                            $newAccessoryIds = array_diff($dto->accessories, $assetsIds);

                            AssetAssignment::insert(
                                array_map(function ($accessoryId) use ($assignment, $dto) {

                                    return [
                                        'assigned_to_id' => $dto->assigned_to_id,
                                        'assigned_at' => Carbon::parse($dto->assign_date)->startOfDay(),
                                        'parent_assignment_id' => $assignment->id,
                                        // 'comment' => "Accesorio asignado junto al equipo principal.",
                                        'asset_id' => $accessoryId,
                                        // 'assigned_at' => $dto->assign_date,
    
                                        // 'created_at' => now()->utc(),
                                        // 'updated_at' => now()->utc(),
                                    ];
                                }, $newAccessoryIds)
                            );
                            Asset::whereIn('id', $newAccessoryIds)->update([
                                'status' => AssetStatus::ASSIGNED->value,
                            ]);
                        }
                    }


                    if ($assignment->wasChanged('assigned_at')) {

                        $changes[] = "Fecha de asignación actualizada de " .
                            $original['assigned_at']->format('d/m/Y') . " a " .
                            $assignment->assigned_at->format('d/m/Y');

                        if ($assignment->childrenAssignments()->count() > 0 && !$accessoriesChanged) {
                            $assignment->childrenAssignments()->update([
                                'assigned_at' => Carbon::parse($dto->assign_date)->startOfDay(),
                            ]);
                        }

                    }


                    $description = implode(',', $changes);

                    // 5️⃣ Historial
                    $this->logHistory(
                        $asset,
                        AssetHistoryAction::ASSIGNED,
                        $description,
                        null
                    );


                    // TODO: Ticket history log
                    // if ($dto->ticket_id) {
                    // app(abstract: TicketService::class)->logHistory(
                    //     $dto->ticket_id,
                    //     TicketHistoryAction::ASSET_ASSIGNED,
                    //     $description . " para el equipo {$asset->full_name}",
                    // );


                    // }

                    return $assignment;
                }




                $asset->update([
                    'status' => AssetStatus::ASSIGNED->value,
                ]);

                // Nueva asignación

                $assigned = AssetAssignment::create(
                    [
                        'assigned_to_id' => $dto->assigned_to_id,
                        'assigned_at' => $dto->assign_date,
                        'comment' => $dto->comment,
                        'asset_id' => $dto->asset_id,


                    ]
                );

                $description = "Equipo asignado a {$assigned->assignedTo->full_name}";
                // $ticketDescription = "Equipo '{$asset->type->name} {$asset->full_name}' asignado a '{$assigned->assignedTo->full_name}'";

                // Asignar accesorios si los hay
                if ($dto->accessories && is_array($dto->accessories)) {
                    AssetAssignment::insert(
                        array_map(function ($accessoryId) use ($assigned, $dto) {

                            return [
                                'assigned_to_id' => $dto->assigned_to_id,
                                'assigned_at' => Carbon::parse($dto->assign_date)->startOfDay(),
                                'parent_assignment_id' => $assigned->id,
                                // 'comment' => "Accesorio asignado junto al equipo principal.",
                                'asset_id' => $accessoryId,

                            ];
                        }, $dto->accessories)


                    );

                    $accesories = Asset::whereIn('id', $dto->accessories ?? [])->get();
                    Asset::whereIn('id', $dto->accessories ?? [])->update([
                        'status' => AssetStatus::ASSIGNED->value,
                    ]);

                    $description = "Equipo asignado a {$assigned->assignedTo->full_name} junto con los accesorios: " . implode(',', $accesories->pluck('full_name')->map(fn($name) => "{$name}")->toArray());
                    // $ticketDescription = "Equipo '{$asset->type->name} {$asset->full_name}' asignado a '{$assigned->assignedTo->full_name}' junto con los accesorios: '" . implode(',', $accesories->pluck('full_name')->map(fn($name) => "{$name}")->toArray()) . "'";


                    AssetHistory::insert(
                        array_map(function ($accessoryId) use ($assigned) {

                            return [
                                'action' => AssetHistoryAction::ASSIGNED->value,
                                'description' => "Accesorio asignado a {$assigned->assignedTo->full_name} junto al equipo principal ({$assigned->asset->full_name})",
                                'asset_id' => $accessoryId,
                                'performed_by' => auth()->user()->staff_id,
                                // 'performed_at' => now()->utc(),
                                // 'related_assignment_id' => $assigned->id,
                            ];
                        }, $dto->accessories)
                    );


                }

                $this->logHistory(
                    $asset,
                    AssetHistoryAction::ASSIGNED,
                    $description,
                    null
                );


                return $assigned;

            });
            return $assignment;
        } catch (\Exception $e) {

            if ($e instanceof BadRequestException || $e instanceof NotFoundHttpException) {
                throw $e;
            }

            throw new InternalErrorException('Error al asignar el activo ' . $e->getMessage());
        }
    }

    public function devolveAsset(AssetAssignment $assignment, DevolveAssetDto $dto)
    {

        $responsible = auth()->user();

        try {
            // DB::transaction(function () use ($assignment, $dto) {
            if ($assignment->returned_at) {
                throw new BadRequestException('La asignación ya ha sido devuelta.');
            }

            // $responsible = auth()->user();

            // if ($responsible->dept_id !== Allowed::SYSTEM_TI->value) {
            //     throw new BadRequestException('El usuario responsable debe pertenecer al departamento de SISTEMAS / TI.');
            // }

            DB::transaction(function () use ($dto, $assignment, $responsible) {
                Asset::where('id', $assignment->asset_id)->update([
                    'status' => AssetStatus::AVAILABLE->value,
                ]);

                $description = "Equipo devuelto por {$assignment->assignedTo->full_name} por " . ReturnReason::labels(ReturnReason::from($dto->return_reason));
                // if ($assignment->parent_assignment_id) {
                //     $parentAsset = $assignment->parentAssignment->asset;
                //     $description .= " Accesorio del equipo principal ({$parentAsset->name} - {$parentAsset->brand} {$parentAsset->model}) devuelto por {$assignment->assignedTo->full_name} por" . ReturnReason::labels(ReturnReason::from($dto->return_reason));
                // }

                if (!$assignment->parent_assignment_id) {
                    $childAssignments = $assignment->childrenAssignments()->select('id', 'asset_id')->get();
                    if (!$childAssignments->isEmpty()) {
                        $asset = $assignment->asset;
                        $description .= " junto con los accesorios: " . $childAssignments->map(function ($childAssignment) {
                            $asset = $childAssignment->asset;
                            return $asset->full_name;
                        })->implode(', ');




                        AssetAssignment::whereIn('id', $childAssignments->pluck('id'))->update([
                            // 'parent_assignment_id' => null,
                            'returned_at' => Carbon::parse($dto->return_date)->toDateTimeString(),
                            'return_comment' => "Devuelto junto al equipo principal ({$asset->name} - {$asset->brand} {$asset->model})",
                            'responsible_id' => $responsible->staff_id,
                            'return_reason' => $dto->return_reason,
                        ]);

                        AssetHistory::insert(
                            $childAssignments->map(function ($childAssignment) use ($dto, $asset, $responsible) {
                                return [
                                    'action' => AssetHistoryAction::RETURNED->value,
                                    'description' => "Accesorio devuelto junto al equipo principal {$asset->full_name} por " . ReturnReason::labels(ReturnReason::from($dto->return_reason)),
                                    'asset_id' => $childAssignment->asset_id,
                                    'performed_by' => $responsible->staff_id,
                                    // 'performed_at' => now()->utc(),
                                    // 'related_assignment_id' => $childAssignment->id,
                                ];
                            })->toArray()
                        );

                        Asset::whereIn('id', $childAssignments->pluck('asset_id'))->update([
                            'status' => AssetStatus::AVAILABLE->value,
                        ]);
                    }
                }

                $assignment->update([
                    'parent_assignment_id' => null,
                    'returned_at' => Carbon::parse($dto->return_date)->toDateTimeString(),
                    'return_comment' => $dto->return_comment,
                    'responsible_id' => $responsible->staff_id,
                    'return_reason' => $dto->return_reason,
                ]);



                $this->logHistory(
                    $assignment->asset,
                    AssetHistoryAction::RETURNED,
                    $description,

                );


            });

            // });
        } catch (\Exception $e) {
            if ($e instanceof BadRequestException || $e instanceof NotFoundHttpException) {
                throw $e;
            }

            throw new InternalErrorException('Error al devolver el activo ' . $e->getMessage());
        }
    }


    private function setCompanyInfoByEmployee(User $employee, TemplateProcessor $template, ?array $size = null)
    {
        if ($employee->id_empresa === EmployeeCompany::CECHRIZA->value) {
            $template->setValue('company', 'CECHRIZA S.A.C');
            $logo = storage_path('app/companies/cechriza-logo.png');
            if ($size) {
                $template->setImageValue('logo', ['path' => $logo, 'width' => $size['width'], 'height' => $size['height']]);
                return;
            }
            $template->setImageValue('logo', ['path' => $logo, 'width' => 280, 'height' => 280]);

        } else if ($employee->id_empresa === EmployeeCompany::YDIEZA->value) {
            $template->setValue('company', 'YDIEZA S.A.C');
            $logo = storage_path('app/companies/ydieza-logo.png');
            if ($size) {
                $template->setImageValue('logo', ['path' => $logo, 'width' => $size['width'], 'height' => $size['height']]);
                return;
            }
            $template->setImageValue('logo', ['path' => $logo, 'width' => 250, 'height' => 250]);
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

            $this->setCompanyInfoByEmployee($assignment->assignedTo, $template);
            $template->setValue('type', strtoupper($asset->type->name));
            $template->setValue('assign_date', $assignment->assigned_at->translatedFormat('d \d\e F \d\e\l Y'));
            $template->setValue('fullname', strtoupper($assignment->assignedTo->full_name));
            $template->setValue('dni', $assignment->assignedTo->dni ?? 'N/A');
            $template->setValue('is_new', $asset->is_new ? 'NUEVO' : 'USADO');
            $template->setValue('brand', $asset->brand);
            $template->setValue('model', $asset->model);
            $template->setValue('serial_number', $asset->serial_number);
            $template->setValue('processor', strtoupper($asset->processor ?? 'N/A'));
            $template->setValue('ram', strtoupper($asset->ram ?? 'N/A'));
            $template->setValue('storage', strtoupper($asset->storage ?? 'N/A'));

            $template->setValue('comment', $assignment->comment ?? '');

            $template->setValue(
                'accessory_list',
                $assignment->childrenAssignments->map(function ($childAssignment) {
                    $asset = $childAssignment->asset;
                    return '- ' . $asset->full_name;
                })->implode('</w:t><w:br/><w:t>')
            );

            $fileName = "cargo_{$asset->type->name}_" . strtolower(str_replace(' ', '_', $assignment->assignedTo->full_name)) . '_' . Carbon::now()->format('Ymd_His') . '.docx';
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


            $accessories = $assignment->childrenAssignments->map(function ($childAssignment) {
                $asset = $childAssignment->asset;
                return '- ' . $asset->full_name;
            })->implode('</w:t><w:br/><w:t>');

            $this->setCompanyInfoByEmployee($assignment->assignedTo, $template, ['width' => 80, 'height' => 60]);
            $template->setValue('assign_date', $assignment->assigned_at->format('d/m/Y'));
            $template->setValue('fullname', strtoupper($assignment->assignedTo->full_name));
            $template->setValue('dni', $assignment->assignedTo->dni ?? 'N/A');
            $template->setValue('department', strtoupper($assignment->assignedTo->charge->descripcion_cargo ?? 'N/A'));
            $template->setValue('is_new', $asset->is_new ? 'NUEVO' : 'USADO EN BUEN ESTADO');
            $template->setValue('brand', $asset->brand);
            $template->setValue('model', $asset->model);
            $template->setValue('accessories', $accessories);
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

    public function generateAccessoryAssignmentDocument(
        AssetAssignment $assignment
    ) {
        try {
            if (!$assignment->assignedTo) {
                throw new BadRequestException('El activo no está asignado a ningún usuario');
            }

            $asset = $assignment->asset;

            Storage::disk('public')->makeDirectory('documents');

            $template = new TemplateProcessor(storage_path('app/templates/cargo-accesorio.docx'));

            $this->setCompanyInfoByEmployee($assignment->assignedTo, $template);
            $template->setValue('assign_date', $assignment->assigned_at->translatedFormat('d \d\e F \d\e\l Y'));
            $template->setValue('fullname', strtoupper($assignment->assignedTo->full_name));
            $template->setValue('dni', $assignment->assignedTo->dni ?? 'N/A');
            $template->setValue('name', $asset->name);
            $template->setValue('brand', $asset->brand);
            $template->setValue('model', $asset->model ?? 'N/A');
            $template->setValue('serial_number', $asset->serial_number ?? 'N/A');
            $template->setValue('is_new', $asset->is_new ? 'NUEVO' : 'USADO');

            $fileName = 'cargo_accesorios_' . strtolower(str_replace(' ', '_', $assignment->assignedTo->full_name)) . '_' . Carbon::now()->format('Ymd_His') . '.docx';
            $path = storage_path('app/public/documents/' . $fileName);
            $template->saveAs($path);

            return $path;
        } catch (\Exception $e) {
            if ($e instanceof BadRequestException) {
                throw $e;
            }

            throw new InternalErrorException('Error al generar el documento de asignación de accesorios');
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

            $type = $asset->type->name;
            if ($type === 'Accesorio') {
                $type = 'Accesorio de ' . $asset->full_name;
            }

            $template->setValue('type', strtoupper($type));
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
            $template->setValue('has_charger', $assignment->childrenAssignments->filter(function ($child) {
                return stripos($child->asset->name, 'cargador') !== null;
            })->count() > 0 ? 'X' : '');
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

    public function uploadDeliveryRecord(AssetAssignment $assignment, UploadDeliveryRecordDto $dto)
    {
        try {
            $current = $assignment;

            if ($assignment->parent_assignment_id) {
                $current = $assignment->parentAssignment;
            }

            $recordType = $dto->type === DeliveryRecordType::ASSIGNMENT->value ? 'asignación' : 'devolución';
            $description = "Constancia de {$recordType} subida para '{$current->assignedTo->full_name}'";

            if ($current->deliveryRecords()->where('type', $dto->type)->exists()) {
                $description = "Constancia de {$recordType} actualizada para '{$current->assignedTo->full_name}'";
            }
            // $path = '';
            // $path = Storage::disk('public')->putFile('delivery_records', $dto->file);
            $path = CompressImage::save($dto->file, 'delivery_records');

            DB::transaction(function () use ($dto, $path, $description, $current) {

                $deliveryRecord = DeliveryRecord::create([
                    'file_path' => $path,
                    'assignment_id' => $current->id,
                    'type' => $dto->type,
                ]);

                $current->deliveryRecords()->save($deliveryRecord);

                AssetHistory::create([
                    'action' => AssetHistoryAction::DELIVERY_RECORD_UPLOADED->value,
                    'description' => $description,
                    'asset_id' => $current->asset_id,
                    'performed_by' => auth()->user()->staff_id,
                    // 'performed_at' => now()->utc(),
                    'related_delivery_record_id' => $deliveryRecord->id,
                ]);
            });



            return Storage::disk('public')->url($path);
        } catch (\Exception $e) {

            if ($e instanceof NotFoundHttpException) {
                throw $e;
            }

            throw new InternalErrorException('Error al subir la constancia de entrega ' . $e->getMessage());
        }
    }

    public function uploadInvoiceDocument(Asset $asset, string $file)
    {

        try {
            // $filePath = Storage::disk('public')->putFile('invoices', $file);
            $filePath = CompressImage::save($file, 'invoices');


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
                    // 'performed_at' => now()->utc(),
                    'invoice_path' => $filePath,

                ]);
            });

            return Storage::disk('public')->url($filePath);

        } catch (\Exception $e) {
            throw new InternalErrorException('Error al subir el documento de factura');
        }
    }


}