<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Services\AssetTypeService;
use App\Services\BrandService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BrandController extends Controller
{
    public function renderBrands(BrandService $brandService, AssetTypeService $assetTypeService)
    {

        return Inertia::render('settings/Brands', [
            'brands' => Inertia::once(fn() => $brandService->getBrands()),
            'types' => Inertia::once(fn() => $assetTypeService->getTypes()),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type_id' => ['required', 'integer', 'exists:assets_type,id'],
        ]);

        $request->validate([
            'name' => [
                Rule::unique('brands', 'name')->where(function ($query) use ($data) {
                    return $query->where('type_id', $data['type_id']);
                }),
            ],
        ]);

        try {
            $brand = Brand::create($data);

            Inertia::flash([
                'brand' => $brand->load('type:id,name'),
                'success' => 'Marca creada exitosamente',
                'error' => null,
            ]);
        } catch (\Exception $e) {
            Inertia::flash([
                'brand' => null,
                'success' => null,
                'error' => 'Error al crear la marca: ' . $e->getMessage(),
            ]);
        }

        return back();
    }

    public function update(Request $request, Brand $brand)
    {
        if ($brand->assets()->exists()) {
            return back()->withErrors([
                'type_id' => 'No puedes editar esta marca porque tiene activos asociados.',
            ]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type_id' => ['required', 'integer', 'exists:assets_type,id'],
        ]);

        $request->validate([
            'name' => [
                Rule::unique('brands', 'name')
                    ->ignore($brand->id)
                    ->where(function ($query) use ($data) {
                        return $query->where('type_id', $data['type_id']);
                    }),
            ],
        ]);

        if ((int) $brand->type_id !== (int) $data['type_id']) {
            if ($brand->models()->exists() || $brand->assets()->exists()) {
                return back()->withErrors([
                    'type_id' => 'No puedes cambiar el tipo de la marca porque tiene modelos o activos asociados.',
                ]);
            }
        }

        try {
            $brand->update($data);

            Inertia::flash([
                'brand' => $brand->fresh()->load('type:id,name'),
                'success' => 'Marca actualizada exitosamente',
                'error' => null,
            ]);
        } catch (\Exception $e) {
            Inertia::flash([
                'brand' => null,
                'success' => null,
                'error' => 'Error al actualizar la marca: ' . $e->getMessage(),
            ]);
        }

        return back();
    }

    public function destroy(Brand $brand)
    {
        try {
            $brand->delete();

            Inertia::flash([
                'success' => 'Marca eliminada exitosamente',
                'error' => null,
            ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getCode() === '23000'
                ? 'No se puede eliminar esta marca porque hay activos asociados a ella.'
                : 'Error al eliminar la marca: ' . $e->getMessage();

            Inertia::flash([
                'success' => null,
                'error' => $errorMessage,
            ]);
        }

        return back();
    }
}
