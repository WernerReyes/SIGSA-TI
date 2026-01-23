<?php

namespace App\Http\Controllers;

use App\DTOs\Asset\AssetFiltersDto;
use App\DTOs\Asset\AssetHistoryFiltersDto;
use App\DTOs\Asset\AssignAssetDto;
use App\DTOs\Asset\DevolveAssetDto;
use App\DTOs\Asset\StoreAssetDto;
use App\DTOs\Asset\UpdateAssetDto;
use App\DTOs\Asset\UploadDeliveryRecordDto;
use App\Enums\Asset\AssetStatus;
use App\Http\Requests\Asset\AssignAssetRequest;
use App\Http\Requests\Asset\DevolveAssetRequest;
use App\Http\Requests\Asset\StoreAssetRequest;
use App\Http\Requests\Asset\UpdateAssetRequest;
use App\Http\Requests\asset\UploadDeliveryRecordRequest;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Services\AssetService;
use App\Services\AssetTypeService;
use App\Services\DepartmentService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetController extends Controller
{
    //

    public function renderView(
        AssetService $assetService,
        AssetTypeService $assetTypeService,
        UserService $userService,
        DepartmentService $departmentService,
        Request $request
    ) {
        $filters = AssetFiltersDto::fromArray($request->all());
        $assetId = $request->input('asset_id');
        $assignmentId = $request->input('assignment_id');

        return Inertia::render('Assets', [
            'filters' => $filters,
            'TIUsers' => Inertia::optional(fn() => $userService->getTIDepartmentUsers())->once(),
            'users' => Inertia::optional(fn() => $userService->getAllBasicInfo())->once(),
            'departments' => Inertia::optional(fn() => $departmentService->getBasicInfo())->once(),
            'types' => Inertia::optional(fn() => $assetTypeService->getTypes())->once(),
            'accessories' => Inertia::optional(fn() => $assetService->getAccessories())->once(),
            'assetsPaginated' => fn() => $assetService->getPaginated($filters),
            'stats' => fn() => $assetService->getStats(),
            'accessoriesOutOfStockAlert' => fn() => $assetService->getAccessoriesOutOfStockAlert(),

            'details' => Inertia::optional(fn() => $assetId ? $assetService->getDetails(Asset::find($assetId)) : null),
            // 'histories' => Inertia::optional(fn() => $assetId ? $assetService->getHistories(Asset::find($assetId)) : null),
            'historiesPaginated' => Inertia::optional(function () use ($assetService, $assetId, $request) {
                if (!$assetId) {
                    return null;
                }
                $filters = AssetHistoryFiltersDto::fromArray($request->all());
                return $assetService->getHistoriesPaginated(Asset::find($assetId), $filters);
            }),
            'assignDocument' => Inertia::optional(fn() => $assignmentId ? $assetService->getAssignDocument(AssetAssignment::find($assignmentId)) : null),
        ]);
    }

    public function resendAccessoryOutOfStockAlert(AssetService $assetService)
    {
        try {
            $assetService->resendAccessoryOutOfStockAlert();

            Inertia::flash([
                'success' => 'Alerta de accesorios fuera de stock reenviada correctamente.',
                'timestamp' => now()->timestamp,
                'error' => null,
            ]);
        } catch (\Exception $e) {
            Inertia::flash([
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);
        }
        return back();
    }

    public function storeAsset(StoreAssetRequest $request, AssetService $assetService)
    {
            
    $validated = $request->validated();
        
        $dto = StoreAssetDto::fromArray($validated);
        try {
            $asset = $assetService->storeAsset($dto);

            Inertia::flash([
                'success' => 'Activo registrado correctamente: ' . $asset->name,
                'timestamp' => now()->timestamp,
                'error' => null,
            ]);
        } catch (\Exception $e) {
            Inertia::flash([
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);
        }
        return back();
    }

    public function updateAsset(UpdateAssetRequest $request, Asset $asset, AssetService $assetService)
    {
        $validated = $request->validated();
        $dto = UpdateAssetDto::fromArray($validated);
        try {
            $asset = $assetService->updateAsset($dto, $asset);

            Inertia::flash([
                'success' => 'Activo actualizado correctamente: ' . $asset->name,
                'timestamp' => now()->timestamp,
                'error' => null,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);

        }
        return back();
    }

    public function deleteAsset(Asset $asset, AssetService $assetService)
    {
        try {
            $assetService->deleteAsset($asset);

            Inertia::flash([
                'success' => 'Activo eliminado correctamente: ' . $asset->name,
                'timestamp' => now()->timestamp,
                'error' => null,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);

        }
        return back();
    }

    public function changeAssetStatus(Request $request, Asset $asset, AssetService $assetService)
    {
        $newStatus = $request->input('status');
        if (!in_array($newStatus, AssetStatus::values())) {
            Inertia::flash([
                'error' => 'Estado de activo inválido proporcionado.',
                'timestamp' => now()->timestamp,
            ]);
            return back();
        }

        try {
            $assetService->changeAssetStatus($asset, $newStatus);

            Inertia::flash([
                'success' => 'Estado de activo actualizado correctamente: ' . $asset->name,
                'timestamp' => now()->timestamp,
                'error' => null,
            ]);
        } catch (\Exception $e) {
            Inertia::flash([
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);
        }
        return back();
    }

    public function assignAsset(AssignAssetRequest $request, AssetService $assetService)
    {

        $validated = $request->validated();
        $dto = AssignAssetDto::fromArray($validated);
        try {
            $assignment = $assetService->assignAsset($dto);

            Inertia::flash([
                'success' => 'Activo asignado correctamente.',
                'assignment_id' => $assignment->id,
                'timestamp' => now()->timestamp,
                'error' => null,
            ]);


        } catch (\Exception $e) {
            Inertia::flash([
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);
        }
        return back();
    }

    public function devolveAsset(DevolveAssetRequest $request, AssetService $assetService, AssetAssignment $assignment)
    {
        $validated = $request->validated();
        $dto = DevolveAssetDto::fromArray($validated);
        try {
            $assetService->devolveAsset($assignment, $dto);

            Inertia::flash([
                'success' => 'Activo devuelto correctamente',
                'timestamp' => now()->timestamp,
                'error' => null,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);

        }
        return back();
    }

    public function generateLaptopAssignmentDocument(AssetAssignment $assignment, AssetService $assetService)
    {
        try {
            $filePath = $assetService->generateLaptopAssignmentDocument($assignment);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Inertia::flash([
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
                
            ]);
            return back();
        }
    }

    public function generateCellphoneAssignmentDocument(AssetAssignment $assignment, AssetService $assetService)
    {
        try {
            $filePath = $assetService->generateCellphoneAssignmentDocument($assignment);


            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Inertia::flash([
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);
            return back();
        }
    }

    public function generateAccessoryAssignmentDocument(AssetAssignment $assignment, AssetService $assetService)
    {
        try {
            $filePath = $assetService->generateAccessoryAssignmentDocument($assignment);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Inertia::flash([
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);
            return back();
        }
    }

    public function generateReturnDocument(AssetAssignment $assignment, AssetService $assetService)
    {
        try {
            $filePath = $assetService->generateReturnDocument($assignment);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Inertia::flash([
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);
            return back();
        }
    }


    public function uploadDeliveryRecord(UploadDeliveryRecordRequest $request, AssetAssignment $assignment, AssetService $assetService)
    {
        $validated = $request->validated();
        $dto = UploadDeliveryRecordDto::fromArray($validated, $assignment);
        try {
            $file_url = $assetService->uploadDeliveryRecord($dto);
            Inertia::flash([
                'success' => 'Registro de entrega subido correctamente.',
                'file_url' => $file_url,
                'timestamp' => now()->timestamp,
                'error' => null,
            ]);
        } catch (\Exception $e) {
            Inertia::flash([
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);
        }
        return back();
    }

    public function uploadInvoiceDocument(Request $request, Asset $asset, AssetService $assetService)
    {
        $request->validate([
            'invoice' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // Max 2MB
        ]);

        try {
            $fileUrl = $assetService->uploadInvoiceDocument($asset, $request->file('invoice'));
            Inertia::flash([
                'success' => 'Factura subida correctamente.',
                'file_url' => $fileUrl,
                'timestamp' => now()->timestamp,
                'error' => null,
            ]);
        } catch (\Exception $e) {
            Inertia::flash([
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);
        }
        return back();
    }
}
