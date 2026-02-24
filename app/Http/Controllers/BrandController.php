<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BrandController extends Controller
{
    public function renderBrands(BrandService $brandService)
    {
      
        return Inertia::render('settings/Brands', [
            'brands' => Inertia::once(fn() => $brandService->getBrands()),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('brands', 'name')],
        ]);

        try {
            $brand = Brand::create($data);

            Inertia::flash([
                'brand' => $brand,
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
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('brands', 'name')->ignore($brand->id)],
        ]);

        try {
            $brand->update($data);

            Inertia::flash([
                'brand' => $brand->fresh(),
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
