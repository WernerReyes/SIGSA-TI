<?php

namespace App\Http\Controllers;

use App\Services\AssetService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetController extends Controller
{
    //

    public function renderView(AssetService $assetService)
    {
        $types = $assetService->getTypes();
        return Inertia::render('Assets', [
            'types' => $types,
        ]);
    }

    public function registerType(Request $request, AssetService $assetService)
    {
        $name = $request->input('name');
        try {
            $assetType = $assetService->registerType($name);
            return back()->with('success', 'Tipo de activo registrado: ' . $assetType->name);
        } catch (\Exception $e) {
            if ($e->getCode() === 23000) { // Integrity constraint violation
                return back()->withErrors(['error' => 'El tipo de activo ya existe.']);
            }
            return back()->withErrors(['error' => 'Ocurrió un error al registrar el tipo de activo. Por favor, inténtelo de nuevo.']);
        }
    }
}
