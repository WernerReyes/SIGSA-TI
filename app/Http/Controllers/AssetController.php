<?php

namespace App\Http\Controllers;

use App\DTOs\Asset\AssetFiltersDto;
use App\DTOs\Asset\AssignAssetDto;
use App\DTOs\Asset\DevolveAssetDto;
use App\DTOs\Asset\StoreAssetDto;
use App\DTOs\Asset\UpdateAssetDto;
use App\DTOs\Asset\UploadDeliveryRecordDto;
use App\Enums\Asset\AssetStatus;
use App\Http\Requests\Asset\AssignAssetRequest;
use App\Http\Requests\asset\DevolveAssetRequest;
use App\Http\Requests\Asset\StoreAssetRequest;
use App\Http\Requests\Asset\UpdateAssetRequest;
use App\Http\Requests\asset\UploadDeliveryRecordRequest;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Services\AssetService;
use App\Services\DepartmentService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetController extends Controller
{
    //

    public function renderView(AssetService $assetService, UserService $userService, DepartmentService $departmentService, Request $request)
    {
        $types = $assetService->getTypes();

        $users = $userService->getAllUsers();
        $TIUsers = $userService->getTIDepartmentUsers();
        $depatments = $departmentService->getAll();

        $filters = AssetFiltersDto::fromArray($request->all());
        $assetsPaginated = $assetService->getPaginated($filters);

        $stats = $assetService->getStats();

        return Inertia::render('Assets', [
            'types' => $types,
            // 'assets' => $assets,
            'users' => $users,
            'TIUsers' => $TIUsers,
            'filters' => $filters,
            'departments' => $depatments,
            'assetsPaginated' => $assetsPaginated,
            'stats' => $stats,
        ]);
    }

    public function registerType(Request $request, AssetService $assetService)
    {
        $name = $request->input('name');
        $id = $request->input('id', null);
        try {
            $assetType = $assetService->registerType($name, $id);

            $message = $id ? 'Tipo de activo actualizado: ' : 'Tipo de activo registrado: ';
            return back()->with('success', $message . $assetType->name);
        } catch (\Exception $e) {
            ds($e->getMessage());
            if ($e->getCode() === 23000) { // Integrity constraint violation
                return back()->withErrors(['error' => 'El tipo de activo ya existe.']);
            }
            return back()->withErrors(['error' => 'Ocurrió un error al registrar el tipo de activo. Por favor, inténtelo de nuevo.']);
        }
    }

    public function deleteType(Request $request, AssetService $assetService)
    {
        $id = $request->input('id');
        try {
            $assetService->deleteType($id);
            return back()->with('success', 'Tipo de activo eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function storeAsset(StoreAssetRequest $request, AssetService $assetService)
    {
        $validated = $request->validated();
        $dto = StoreAssetDto::fromArray($validated);
        try {
            $asset = $assetService->storeAsset($dto);
            return back()->with('success', 'Activo registrado correctamente: ' . $asset->name);
        } catch (\Exception $e) {
            ds($e->getMessage());
            if ($e->getCode() !== 500) {
                return back()->withErrors(['error' => $e->getMessage()]);
            }
            return back()->withErrors(['error' => 'Ocurrió un error al registrar el activo. Por favor, inténtelo de nuevo.']);
        }
    }

    public function updateAsset(UpdateAssetRequest $request, AssetService $assetService)
    {
        $validated = $request->validated();
        $dto = UpdateAssetDto::fromArray($validated);
        try {
            $asset = $assetService->updateAsset($dto);
            return back()->with('success', 'Activo actualizado correctamente: ' . $asset->name);
        } catch (\Exception $e) {
            ds($e->getMessage());
            if ($e->getCode() !== 500) {
                return back()->withErrors(['error' => $e->getMessage()]);
            }
            return back()->withErrors(['error' => 'Ocurrió un error al actualizar el activo. Por favor, inténtelo de nuevo.']);
        }
    }

    public function changeAssetStatus(Request $request, AssetService $assetService)
    {
        $assetId = $request->input('asset_id');
        $newStatus = $request->input('status');
        if (!in_array($newStatus, AssetStatus::values())) {
            return back()->withErrors(['error' => 'Estado de activo inválido proporcionado.']);
        }

        try {
            $assetService->changeAssetStatus($assetId, $newStatus);
            return back()->with('success', 'Estado del activo actualizado correctamente.');
        } catch (\Exception $e) {
            ds($e->getMessage());
            if ($e->getCode() !== 500) {
                return back()->withErrors(['error' => $e->getMessage()]);
            }
            return back()->withErrors(['error' => 'Ocurrió un error al actualizar el estado del activo. Por favor, inténtelo de nuevo.']);
        }
    }

    public function assignAsset(AssignAssetRequest $request, AssetService $assetService)
    {
        $validated = $request->validated();
        $dto = AssignAssetDto::fromArray($validated);
        try {
            $assetService->assignAsset($dto);
            return back()->with('success', 'Activo asignado correctamente');
        } catch (\Exception $e) {
            ds($e->getMessage());
            if ($e->getCode() !== 500) {
                return back()->withErrors(['error' => $e->getMessage()]);
            }
            return back()->withErrors(['error' => 'Ocurrió un error al asignar el activo. Por favor, inténtelo de nuevo.']);
        }
    }

    public function devolveAsset(DevolveAssetRequest $request, AssetService $assetService, AssetAssignment $assignment)
    {
        $validated = $request->validated();
        $dto = DevolveAssetDto::fromArray($validated, $assignment);
        try {
            $assetService->devolveAsset($dto);
            return back()->with('success', 'Activo devuelto correctamente');
        } catch (\Exception $e) {
            ds($e->getMessage());
            if ($e->getCode() !== 500) {
                return back()->withErrors(['error' => $e->getMessage()]);
            }
            return back()->withErrors(['error' => 'Ocurrió un error al devolver el activo. Por favor, inténtelo de nuevo.']);
        }
    }

    public function generateLaptopAssignmentDocument(int $assignmentId, AssetService $assetService)
    {
        try {
            $filePath = $assetService->generateLaptopAssignmentDocument($assignmentId);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            ds($e->getMessage());
            if ($e->getCode() !== 500) {
                return back()->withErrors(['error' => $e->getMessage()]);
            }
            return back()->withErrors(['error' => 'Ocurrió un error al generar el documento. Por favor, inténtelo de nuevo.']);
        }
    }

    public function generateCellphoneAssignmentDocument(int $assetId, AssetService $assetService)
    {
        try {
            $filePath = $assetService->generateCellphoneAssignmentDocument($assetId);
            ds($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            ds($e->getMessage(), $e->getCode());
            if ($e->getCode() !== 500) {
                return back()->withErrors(['error' => $e->getMessage()]);
            }
            return back()->withErrors(['error' => 'Ocurrió un error al generar el documento. Por favor, inténtelo de nuevo.']);
        }
    }


    public function uploadDeliveryRecord(UploadDeliveryRecordRequest $request, AssetAssignment $assignment, AssetService $assetService)
    {
        $validated = $request->validated();
        $dto = UploadDeliveryRecordDto::fromArray($validated, $assignment);
        try {
            $file_url = $assetService->uploadDeliveryRecord($dto);
            return back()->with('success', 'Registro de entrega subido correctamente.')->with('file_url', $file_url);
        } catch (\Exception $e) {
            ds($e->getMessage());
            if ($e->getCode() !== 500) {
                return back()->withErrors(['error' => $e->getMessage()]);
            }
            return back()->withErrors(['error' => 'Ocurrió un error al subir el registro de entrega. Por favor, inténtelo de nuevo.']);
        }
    }

    public function uploadInvoiceDocument(Request $request, Asset $asset, AssetService $assetService)
    {
        $request->validate([
            'invoice' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // Max 2MB
        ]);

        try {
            $fileUrl = $assetService->uploadInvoiceDocument($asset, $request->file('invoice'));
            return back()->with('success', 'Factura subida correctamente.')->with('file_url', $fileUrl);
        } catch (\Exception $e) {
            ds($e->getMessage());
            if ($e->getCode() !== 500) {
                return back()->withErrors(['error' => $e->getMessage()]);
            }
            return back()->withErrors(['error' => 'Ocurrió un error al subir la factura. Por favor, inténtelo de nuevo.']);
        }
    }
}
