<?php
namespace App\Http\Controllers;


use App\Models\AssetType;
use App\Services\AssetTypeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AssetTypeController extends Controller
{

    public function renderTypes(Request $request, AssetTypeService $assetTypeService)
    {
        $component = $request->input('component', 'settings/AssetTypes');
        return Inertia::render($component, [
            'types' => $assetTypeService->getTypes(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'is_accessory' => 'boolean',
        ]);
        try {
            $assetType = AssetType::create($data);
            Inertia::flash([
                'assetType' => $assetType,
                'success' => 'Tipo de activo creado exitosamente',
                'error' => null
            ]);
        } catch (\Exception $e) {
            Inertia::flash([
                'assetType' => null,
                'error' => 'Error al crear el tipo de activo: ' . $e->getMessage(),
                'success' => null
            ]);


        }

        return back();
    }

    public function update(Request $request, AssetType $type)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'is_accessory' => 'boolean',
        ]);
        try {

            $type = $type->update($data);
            Inertia::flash([
                'assetType' => $type,
                'success' => 'Tipo de activo actualizado exitosamente',
                'error' => null
            ]);
        } catch (\Exception $e) {
            Inertia::flash([
                'assetType' => null,
                'error' => 'Error al actualizar el tipo de activo: ' . $e->getMessage(),
                'success' => null
            ]);


        }

        return back();
    }

    public function destroy(AssetType $type)
    {
        if (!$type->is_deletable) {
            throw new BadRequestException('Este tipo de activo no se puede eliminar');
        }

        try {
            $type->delete();
            Inertia::flash([
                'success' => 'Tipo de activo eliminado exitosamente',
                'error' => null
            ]);
        } catch (\Exception $e) {
            if ($e->getCode() === '23000') { // Código de error de integridad referencial
                $errorMessage = 'No se puede eliminar este tipo de activo porque hay activos asociados a él.';
            } else {
                $errorMessage = 'Error al eliminar el tipo de activo: ' . $e->getMessage();
            }
            Inertia::flash([
                'error' => $errorMessage,
                'success' => null
            ]);

        }

        return back();

    }
}
