<?php

namespace App\Http\Controllers;


use App\DTOs\DevelopmentRequest\EstimateDevelopmentDto;
use App\DTOs\DevelopmentRequest\StoreDevelopmentRequestDto;
use App\Enums\DevelopmentRequest\DevelopmentApprovalStatus;
use App\Enums\DevelopmentRequest\DevelopmentRequestStatus;
use App\Http\Requests\DevelopmentRequest\EstimateDevelopmentRequest;
use App\Http\Requests\DevelopmentRequest\StoreDevelopmentRequest;
use App\Models\DevelopmentRequest;
use App\Services\AreaService;
use App\Services\DevelopmentRequestService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DevelopmentController extends Controller
{
    public function renderView(DevelopmentRequestService $service, AreaService $areaService)
    {
        return Inertia::render('Developments', [
            'developments' => Inertia::once(fn() => $service->getAll()),
            'areas' => Inertia::optional(fn() => $areaService->getAll())
        ]);
    }

    public function store(StoreDevelopmentRequest $request, DevelopmentRequestService $service)
    {
        $validated = $request->validated();
        $dto = StoreDevelopmentRequestDto::fromArray($validated);

        try {
           $devRequest = $service->store($dto);

            Inertia::flash([
                'success' => 'Solicitud de desarrollo creada exitosamente.',
                'devRequest' => $devRequest,
                'error' => null,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'devRequest' => null,
                'error' => $e->getMessage(),
            ]);
        }

        return back();
    }

    public function update(StoreDevelopmentRequest $request, DevelopmentRequest $developmentRequest, DevelopmentRequestService $service)
    {
        ds($request->all());
        $validated = $request->validated();
        $dto = StoreDevelopmentRequestDto::fromArray($validated);

        try {

            $devRequest = $service->update($developmentRequest, $dto);

            Inertia::flash([
                'success' => 'Solicitud de desarrollo actualizada exitosamente.',
                'devRequest' => $devRequest,
                'error' => null,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'devRequest' => null,
                'error' => $e->getMessage(),
            ]);
        }

        return back();
    }

    public function updateStatus(Request $request, DevelopmentRequest $developmentRequest, DevelopmentRequestService $service)
    {
        $request->validate([
            'new_status' => 'required|string|in:' . DevelopmentRequestStatus::implodeValues()
        ]);

        $newStatus = $request->input('new_status');

        try {

            $service->updateStatus($developmentRequest, $newStatus);

            Inertia::flash([
                'success' => 'Estado de la solicitud de desarrollo actualizado exitosamente.',
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


    public function estimateDevelopment(EstimateDevelopmentRequest $request, DevelopmentRequest $developmentRequest, DevelopmentRequestService $service)
    {
        $validated = $request->validated();
        $dto = EstimateDevelopmentDto::fromArray($validated);

        try {

            $service->estimateDevelopment($developmentRequest, $dto);

            Inertia::flash([
                'success' => 'Estimación de la solicitud de desarrollo guardada exitosamente.',
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
