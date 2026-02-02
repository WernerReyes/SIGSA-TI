<?php

namespace App\Http\Controllers;


use App\DTOs\DevelopmentRequest\ApproveDevelopmentDto;
use App\DTOs\DevelopmentRequest\EstimateDevelopmentDto;
use App\DTOs\DevelopmentRequest\StoreDevelopmentRequestDto;
use App\Enums\DevelopmentRequest\DevelopmentApprovalStatus;
use App\Enums\DevelopmentRequest\DevelopmentRequestStatus;
use App\Http\Requests\DevelopmentRequest\ApproveDevelopmentRequest;
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
            'developmentsByStatus' => Inertia::once(fn() => $service->getSectionsByStatus()),
            'areas' => Inertia::optional(fn() => $areaService->getAll())->once()
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


    public function delete(DevelopmentRequest $developmentRequest, DevelopmentRequestService $service)
    {
        try {

            $service->delete($developmentRequest);

            Inertia::flash([
                'success' => 'Solicitud de desarrollo eliminada exitosamente.',
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


    public function swapPositions(Request $request, DevelopmentRequestService $service)
    {
        $request->validate([
            'devs_ids_in_order' => 'required|array',
            'devs_ids_in_order.*' => 'integer|exists:development_requests,id',
            'status' => 'required|string|in:' . DevelopmentRequestStatus::implodeValues()
        ]);

        $devsIdsInOrder = $request->input('devs_ids_in_order');
        $status = $request->input('status');
 
        try {

            $service->swapPositions($devsIdsInOrder, $status);

            Inertia::flash([
                'success' => 'Posiciones de las solicitudes de desarrollo actualizadas exitosamente.',
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

    public function updateStatus(Request $request, DevelopmentRequest $developmentRequest, DevelopmentRequestService $service)
    {
        $request->validate([
            'new_status' => 'required|string|in:' . DevelopmentRequestStatus::implodeValues()
        ]);

        $newStatus = $request->input('new_status');
        $devsIdsInOrder = $request->input('devs_ids_in_order', []);

        try {

            $service->updateStatus($developmentRequest, $newStatus, $devsIdsInOrder);

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


    public function estimate(EstimateDevelopmentRequest $request, DevelopmentRequest $developmentRequest, DevelopmentRequestService $service)
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

    public function approveTechnical(ApproveDevelopmentRequest $request, DevelopmentRequest $developmentRequest, DevelopmentRequestService $service)
    {
        $validated = $request->validated();
        $dto = ApproveDevelopmentDto::fromArray($validated);

      

        try {
            $service->approveTechnicalDevelopment($developmentRequest, $dto);

            $message = $dto->status === DevelopmentApprovalStatus::APPROVED
                ? 'Solicitud de desarrollo aprobada técnicamente exitosamente.'
                : 'Solicitud de desarrollo rechazada técnicamente exitosamente.';

            Inertia::flash([
                'success' => $message,
                'error' => null,
                // 'approval' => $approval,
            ]);

            

        } catch (\Exception $e) {
            
            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage(),
                // 'approval' => null,
            ]);
        }

        return back();
    }

    public function approveStrategic(ApproveDevelopmentRequest $request, DevelopmentRequest $developmentRequest, DevelopmentRequestService $service)
    {
        $validated = $request->validated();
        $dto = ApproveDevelopmentDto::fromArray($validated);

        try {
            $approval = $service->approveStrategicDevelopment($developmentRequest, $dto);

            Inertia::flash([
                'success' => 'Aprobación estratégica de la solicitud de desarrollo guardada exitosamente.',
                'error' => null,
                'approval' => $approval,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage(),
                'approval' => null,
            ]);
        }

        return back();
    }
}