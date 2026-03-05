<?php
namespace App\Http\Controllers;


use App\Enums\Asset\AssetTypeCategory;
use App\Enums\Department\Allowed;
use App\Services\AssetModelService;
use App\Models\AssetType;
use App\Services\AssetTypeService;
use App\Services\BrandService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AssetTypeController extends Controller
{

    public function renderTypes(
        Request $request,
        AssetTypeService $assetTypeService,
        BrandService $brandService,
        AssetModelService $assetModelService,
    ) {
        $component = $request->input('component', 'settings/AssetTypes');
        return Inertia::render($component, [
            'types' => Inertia::once(fn() => $assetTypeService->getTypes()),
            'brands' => Inertia::once(fn() => $brandService->getBrands()),
            'models' => Inertia::once(fn() => $assetModelService->getModels()),
        ]);
    }

    public function store(Request $request)
    {
        try {
        if (auth()->user()->dept_id != Allowed::SYSTEM_TI->value) {
            throw new BadRequestException('No tienes permiso para crear tipos de activos');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'doc_category' => ['required', Rule::in(AssetTypeCategory::values())],
        ]);
            $assetType = AssetType::create($data);
            ds($assetType);
            Inertia::flash([
                'assetType' => $assetType,
                'success' => 'Tipo de activo creado exitosamente',
                'error' => null
            ]);
        } catch (\Exception $e) {
            Inertia::flash([
                'assetType' => null,
                'error' => $e->getMessage(),
                'success' => null
            ]);


        }

        return back();
    }

    public function update(Request $request, AssetType $type)
    {
        try {
            if (auth()->user()->dept_id != Allowed::SYSTEM_TI->value) {
                throw new BadRequestException('No tienes permiso para editar tipos de activos');
            }


            $data = $request->validate([
                'name' => 'required|string|max:255',
                'doc_category' => ['required', Rule::in(AssetTypeCategory::values())],
            ]);

            $type->update($data);
            Inertia::flash([
                'assetType' => $type->fresh(),
                'success' => 'Tipo de activo actualizado exitosamente',
                'error' => null
            ]);
        } catch (\Exception $e) {
            Inertia::flash([
                'assetType' => null,
                'error' =>  $e->getMessage(),
                'success' => null
            ]);


        }

        return back();
    }

    public function destroy(AssetType $type)
    {

        try {
            if (auth()->user()->dept_id != Allowed::SYSTEM_TI->value) {
                throw new BadRequestException('No tienes permiso para eliminar tipos de activos');
            }


            if (!$type->is_deletable) {
                throw new BadRequestException('Este tipo de activo no se puede eliminar');
            }

            if ($type->assets()->exists()) {
                throw new BadRequestException('No se puede eliminar este tipo de activo porque hay activos asociados a él.');
            }

            if ($type->models()->exists()) {
                throw new BadRequestException('No se puede eliminar este tipo de activo porque hay modelos asociados a él.');
            }

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
