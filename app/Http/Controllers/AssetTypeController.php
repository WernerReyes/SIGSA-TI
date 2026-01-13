<?php

namespace App\Http\Controllers;


use App\Services\AssetTypeService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetTypeController extends Controller
{

    public function renderTypes(Request $request, AssetTypeService $assetTypeService)
    {
        $component = $request->input('component', 'Settings/AssetTypes');
        return Inertia::render($component, [
            'types' => $assetTypeService->getTypes(),
        ]);
    }
}
