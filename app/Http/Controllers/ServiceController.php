<?php

namespace App\Http\Controllers;

use App\DTOs\Service\StoreServiceDto;
use App\Http\Requests\Service\StoreServiceRequest;
use App\Models\Service;
use App\Services\CloudService;
use Illuminate\Http\Request;
use Inertia\Inertia;


class ServiceController extends Controller
{
    public function renderView(CloudService $cloudService)
    {

        return Inertia::render('Access', [
            'services' => Inertia::once(fn() => $cloudService->getAll()),
        ]);
    }

    public function store(StoreServiceRequest $request, CloudService $cloudService)
    {
        $validated = $request->validated();
        $dto = StoreServiceDto::fromArray($validated);

        try {
            $service = $cloudService->store($dto);

            Inertia::flash([
                'success' => 'Servicio creado exitosamente.',
                'service' => $service,
                'error' => null,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'service' => null,
                'error' => $e->getMessage(),
            ]);

        }

        return back();

    }


    public function update(Service $service, StoreServiceRequest $request, CloudService $cloudService)
    {
        $validated = $request->validated();
        $dto = StoreServiceDto::fromArray($validated);

        try {
            $updatedService = $cloudService->update($service, $dto);

            Inertia::flash([
                'success' => 'Servicio actualizado exitosamente.',
                'service' => $updatedService,
                'error' => null,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'service' => null,
                'error' => $e->getMessage(),
            ]);

        }

        return back();
    }

    public function delete(Service $service, CloudService $cloudService)
    {
        try {
            $cloudService->delete($service);

            Inertia::flash([
                'success' => 'Servicio eliminado exitosamente.',
                'error' => null,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage(),
            ]);

        }

        return back();
    }
}
