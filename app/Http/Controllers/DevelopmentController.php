<?php

namespace App\Http\Controllers;


use App\DTOs\DevelopmentRequest\StoreDevelopmentRequestDto;
use App\Http\Requests\DevelopmentRequest\StoreDevelopmentRequest;
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
            $service->store($dto);

            Inertia::flash([
                'success' => 'Solicitud de desarrollo creada exitosamente.',
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
