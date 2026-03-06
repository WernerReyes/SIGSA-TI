<?php

namespace App\Http\Controllers;

use App\Services\BrandService;
use App\Services\AssetTypeService;
use App\Models\AssetModel;
use App\Services\AssetModelService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AssetModelController extends Controller
{
    public function renderModels(
        AssetModelService $assetModelService,
        BrandService $brandService,
        AssetTypeService $assetTypeService,
    )
    {
    
        return Inertia::render('settings/Models', [
            'models' => Inertia::once(fn() => $assetModelService->getModels()),
            'brands' => Inertia::once(fn() => $brandService->getBrands()),
            'types' => Inertia::once(fn() => $assetTypeService->getTypes()),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
        ]);

        $request->validate([
            'name' => [
                Rule::unique('models', 'name')->where(function ($query) use ($data) {
                    return $query
                        ->where('brand_id', $data['brand_id']);
                }),
            ],
        ]);

        try {
            $model = AssetModel::create($data);

            Inertia::flash([
                'model' => $model->load(['brand:id,name,type_id', 'brand.type:id,name']),
                'success' => 'Modelo creado exitosamente',
                'error' => null,
            ]);
        } catch (\Exception $e) {
            Inertia::flash([
                'model' => null,
                'success' => null,
                'error' => 'Error al crear el modelo: ' . $e->getMessage(),
            ]);
        }

        return back();
    }

    public function update(Request $request, AssetModel $model)
    {
        if ($model->assets()->exists()) {
            return back()->withErrors([
                'brand_id' => 'No puedes editar este modelo porque tiene activos asociados.',
            ]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
        ]);

        $request->validate([
            'name' => [
                Rule::unique('models', 'name')
                    ->ignore($model->id)
                    ->where(function ($query) use ($data) {
                        return $query
                            ->where('brand_id', $data['brand_id']);
                    }),
            ],
        ]);

        if ((int) $model->brand_id !== (int) $data['brand_id'] && $model->assets()->exists()) {
            return back()->withErrors([
                'brand_id' => 'No puedes cambiar la marca de este modelo porque está asociado a activos.',
            ]);
        }

        try {
            $model->update($data);

            Inertia::flash([
                'model' => $model->fresh()->load(['brand:id,name,type_id', 'brand.type:id,name']),
                'success' => 'Modelo actualizado exitosamente',
                'error' => null,
            ]);
        } catch (\Exception $e) {
            Inertia::flash([
                'model' => null,
                'success' => null,
                'error' => 'Error al actualizar el modelo: ' . $e->getMessage(),
            ]);
        }

        return back();
    }

    public function destroy(AssetModel $model)
    {
        try {
            $model->delete();

            

            Inertia::flash([
                'success' => 'Modelo eliminado exitosamente',
                'error' => null,
            ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getCode() === '23000'
                ? 'No se puede eliminar este modelo porque hay activos asociados a él.'
                : 'Error al eliminar el modelo: ' . $e->getMessage();

            Inertia::flash([
                'success' => null,
                'error' => $errorMessage,
            ]);
        }

        return back();
    }
}
