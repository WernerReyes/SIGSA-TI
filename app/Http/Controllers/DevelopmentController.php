<?php

namespace App\Http\Controllers;


use App\DTOs\DevelopmentRequest\ApproveDevelopmentDto;
use App\DTOs\DevelopmentRequest\EstimateDevelopmentDto;
use App\DTOs\DevelopmentRequest\StoreDevelopmentProgressDto;
use App\DTOs\DevelopmentRequest\StoreDevelopmentRequestDto;
use App\Enums\DevelopmentRequest\DevelopmentApprovalStatus;
use App\Enums\DevelopmentRequest\DevelopmentRequestStatus;
use App\Http\Requests\DevelopmentRequest\ApproveDevelopmentRequest;
use App\Http\Requests\DevelopmentRequest\EstimateDevelopmentRequest;
use App\Http\Requests\DevelopmentRequest\RegisterProgressRequest;
use App\Http\Requests\DevelopmentRequest\StoreDevelopmentRequest;
use App\Models\DevelopmentRequest;
use App\Services\AreaService;
use App\Services\DevelopmentRequestService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DevelopmentController extends Controller
{
    public function renderView(Request $request, DevelopmentRequestService $service, AreaService $areaService, UserService $userService)
    {
        $depReqId = $request->input('development_request_id', null);

        return Inertia::render('Developments', [
            'developmentsByStatus' => Inertia::once(fn() => $service->getSectionsByStatus()),
            'areas' => Inertia::optional(fn() => $areaService->getAll())->once(),
            'TIUsers' => Inertia::optional(fn() => $userService->getTIDepartmentUsers())->once(),
            'progressHistory' => Inertia::optional(fn() => $depReqId ? $service->getProgressHistory($depReqId) : [])->once(),
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
                'timestamp' => now()->timestamp,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'devRequest' => null,
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
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
                'timestamp' => now()->timestamp,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'devRequest' => null,
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
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
                'timestamp' => now()->timestamp,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
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
                'timestamp' => now()->timestamp,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
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

            $result = $service->updateStatus($developmentRequest, $newStatus, $devsIdsInOrder);

            Inertia::flash([
                'success' => 'Estado de la solicitud de desarrollo actualizado exitosamente.',
                'error' => null,
                'progress' => $result['progress'] ?? null,
                'completed' => $result['completed'] ?? null,
                'timestamp' => now()->timestamp,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage(),
                'progress' => null,
                'completed' => null,
                'timestamp' => now()->timestamp,
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
                'timestamp' => now()->timestamp,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
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
                'timestamp' => now()->timestamp,
                // 'approval' => $approval,
            ]);



        } catch (\Exception $e) {

            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
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
            $service->approveStrategicDevelopment($developmentRequest, $dto);

            Inertia::flash([
                'success' => 'Aprobación estratégica de la solicitud de desarrollo guardada exitosamente.',
                'error' => null,
                'timestamp' => now()->timestamp,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);
        }

        return back();
    }

    public function registerProgress(RegisterProgressRequest $request, DevelopmentRequest $developmentRequest, DevelopmentRequestService $service)
    {
        $validated = $request->validated();
        $dto = StoreDevelopmentProgressDto::fromArray($validated);

        try {
            $progress = $service->registerProgress($developmentRequest, $dto);

            Inertia::flash([
                'success' => 'Progreso registrado exitosamente.',
                'error' => null,
                'progress' => $progress,
                'timestamp' => now()->timestamp,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage(),
                'progress' => null,
                'timestamp' => now()->timestamp,
            ]);
        }

        return back();
    }

    public function updateProjectUrl(Request $request, DevelopmentRequest $developmentRequest)
    {
        $request->validate([
            'project_url' => 'nullable|string|url',
        ]);

        $projectUrl = $request->input('project_url', null);

        try {
            $developmentRequest->update([
                'project_url' => $projectUrl,
            ]);

            Inertia::flash([
                'success' => 'URL del proyecto actualizada exitosamente.',
                'error' => null,
                'timestamp' => now()->timestamp,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);
        }

        return back();
    }

    public function assignDevelopers(Request $request, DevelopmentRequest $developmentRequest, DevelopmentRequestService $service)
    {
        $request->validate([
            'developer_ids' => 'array|required',
            'developer_ids.*' => 'integer|exists:ost_staff,staff_id',
        ]);

        try {
            $service->assignDevelopers(
                $developmentRequest,
                $request->input('developer_ids', [])
            );

            Inertia::flash([
                'success' => 'Desarrolladores asignados exitosamente.',
                'error' => null,
                'timestamp' => now()->timestamp,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);
        }

        return back();
    }


    public function comeBackToAnalysis(Request $request, DevelopmentRequest $developmentRequest, DevelopmentRequestService $service)
    {
        try {
            $devsIdsInOrder = $request->input('devs_ids_in_order', []);

            $service->comeBackToAnalysis($developmentRequest, $devsIdsInOrder);

            Inertia::flash([
                'success' => 'La solicitud de desarrollo ha sido devuelta a análisis exitosamente.',
                'error' => null,
                'timestamp' => now()->timestamp,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);
        }

        return back();
    }


}