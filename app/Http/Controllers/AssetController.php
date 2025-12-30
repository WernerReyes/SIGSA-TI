<?php

namespace App\Http\Controllers;

use App\DTOs\Asset\AssignAssetDto;
use App\DTOs\Asset\StoreAssetDto;
use App\DTOs\Asset\UpdateAssetDto;
use App\Http\Requests\Asset\AssignAssetRequest;
use App\Http\Requests\Asset\StoreAssetRequest;
use App\Http\Requests\Asset\UpdateAssetRequest;
use App\Services\AssetService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetController extends Controller
{
    //

    public function renderView(AssetService $assetService, UserService $userService)
    {
        $types = $assetService->getTypes();
        $assets = $assetService->getAll();
        $users = $userService->getAllUsers();
        ds($assets->toArray());
        return Inertia::render('Assets', [
            'types' => $types,
            'assets' => $assets,
            'users' => $users,
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
            ds($e->getMessage());
            if ($e->getCode() !== 500) {
                return back()->withErrors(['error' => $e->getMessage()]);
            }
            return back()->withErrors(['error' => 'Ocurrió un error al eliminar el tipo de activo. Por favor, inténtelo de nuevo.']);
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
}
