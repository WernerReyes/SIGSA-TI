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
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetController extends Controller
{
    //

    public function renderView(AssetService $assetService, UserService $userService, Request $request)
    {
        $TIUsers = $userService->getTIDepartmentUsers();
      
        $filters = AssetFiltersDto::fromArray($request->all());
        $assetsPaginated = $assetService->getPaginated($filters);

        $stats = $assetService->getStats();

        return Inertia::render('Assets', [
            'TIUsers' => $TIUsers,
            'filters' => $filters,
            'assetsPaginated' => $assetsPaginated,
            'stats' => $stats,
        ]);
    }

    public function renderAccessories(AssetService $assetService)
    {
        $assetAccessories = $assetService->getAccessories();
        return Inertia::render('Assets', [
            'assetAccessories' => $assetAccessories,
        ]);
    }

    public function renderAsset(Asset $asset, AssetService $assetService)
    {
        $asset = $assetService->getDetails($asset);

        return Inertia::render('Assets', [
            'asset' => $asset,
        ]);
    }
    

    public function renderHistories(Asset $asset, AssetService $assetService)
    {
        $asset = $assetService->getHistories($asset);

        return Inertia::render('Assets', [
            'asset' => $asset,
        ]);
    }

    

    public function storeAsset(StoreAssetRequest $request, AssetService $assetService)
    {
        $validated = $request->validated();
        $dto = StoreAssetDto::fromArray($validated);
        try {
            $asset = $assetService->storeAsset($dto);
            return back()->with('success', 'Activo registrado correctamente: ' . $asset->name);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function updateAsset(UpdateAssetRequest $request, Asset $asset, AssetService $assetService)
    {
        $validated = $request->validated();
        $dto = UpdateAssetDto::fromArray($validated);
        try {
            $asset = $assetService->updateAsset($dto, $asset);
            return back()->with('success', 'Activo actualizado correctamente: ' . $asset->name);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function changeAssetStatus(Request $request, Asset $asset, AssetService $assetService)
    {
        $newStatus = $request->input('status');
        if (!in_array($newStatus, AssetStatus::values())) {
            return back()->withErrors(['error' => 'Estado de activo inválido proporcionado.']);
        }

        try {
            $assetService->changeAssetStatus($asset, $newStatus);
            return back()->with('success', 'Estado del activo actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function assignAsset(AssignAssetRequest $request, AssetService $assetService)
    {
        $validated = $request->validated();
        $dto = AssignAssetDto::fromArray($validated);
        try {
            $assignment = $assetService->assignAsset($dto);
            return back()->with([
                'success' => 'Activo asignado correctamente.',
                'assignment_id' => $assignment->id,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
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
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function generateLaptopAssignmentDocument(AssetAssignment $assignment, AssetService $assetService)
    {
        try {
            $filePath = $assetService->generateLaptopAssignmentDocument($assignment);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function generateCellphoneAssignmentDocument(AssetAssignment $assignment, AssetService $assetService)
    {
        try {
            $filePath = $assetService->generateCellphoneAssignmentDocument($assignment);


            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);


        }
    }

    public function generateAccessoryAssignmentDocument(AssetAssignment $assignment, AssetService $assetService)
    {
        try {
            $filePath = $assetService->generateAccessoryAssignmentDocument($assignment);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function generateReturnDocument(AssetAssignment $assignment, AssetService $assetService)
    {
        try {
            $filePath = $assetService->generateReturnDocument($assignment);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {

            return back()->withErrors(['error' => $e->getMessage()]);


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
            return back()->withErrors(['error' => $e->getMessage()]);



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
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
