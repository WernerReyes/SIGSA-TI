<?php

namespace App\Http\Controllers;


use App\DTOs\AdminControl\StoreContractDto;
use App\Http\Requests\AdminControl\StoreContractRequest;
use App\Services\AdminControlService;
use Inertia\Inertia;

class AdminControlController extends Controller
{
    //

    public function renderView(AdminControlService $service)
    {
        return Inertia::render('AdminControl', [
            'contracts' => Inertia::once(fn() => $service->getContracts()),
        ]);
    }


    public function storeContract(StoreContractRequest $request, AdminControlService $service)
    {
        $validated = $request->validated();
        $dto = StoreContractDto::fromArray($validated);

        try {
            $contract = $service->storeContract($dto);

            Inertia::flash([
                'success' => 'Contrato creado exitosamente.',
                'contract' => $contract,
                'error' => null
            ]);
        } catch (\Exception $e) {
       
            Inertia::flash([
                'success' => null,
                'contract' => null,
                'error' => $e->getMessage()
            ]);
        }

        return back();
    }
}
