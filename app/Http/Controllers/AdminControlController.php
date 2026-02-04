<?php

namespace App\Http\Controllers;


use App\DTOs\AdminControl\StoreContractDto;
use App\Http\Requests\AdminControl\StoreContractRequest;
use App\Services\AdminControlService;
use Inertia\Inertia;

class AdminControlController extends Controller
{
    //

    public function renderView()
    {
        return Inertia::render('AdminControl');
    }


    public function storeContract(StoreContractRequest $request, AdminControlService $service)
    {
        $validated = $request->validated();
        $dto = StoreContractDto::fromArray($validated);

        try {
            $service->storeContract($dto);

            Inertia::flash([
                'success' => 'Contrato creado exitosamente.',
                'error' => null
            ]);
        } catch (\Exception $e) {

            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage()
            ]);
        }

        return back();
    }
}
