<?php

namespace App\Http\Controllers;

use App\Models\AssetModel;
use App\Services\AssetModelService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AssetModelController extends Controller
{
    public function renderModels(AssetModelService $assetModelService)
    {
    
        return Inertia::render('settings/Models', [
            'models' => Inertia::once(fn() => $assetModelService->getModels()),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('models', 'name')],
        ]);

        try {
            $model = AssetModel::create($data);

            Inertia::flash([
                'model' => $model,
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
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('models', 'name')->ignore($model->id)],
        ]);

        try {
            $model->update($data);

            Inertia::flash([
                'model' => $model->fresh(),
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
