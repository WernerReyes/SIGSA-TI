<?php

namespace App\Http\Controllers;


use App\DTOs\AdminControl\RenewContractDto;
use App\DTOs\AdminControl\StoreContractDto;
use App\Http\Requests\AdminControl\RenewContractRequest;
use App\Http\Requests\AdminControl\StoreContractRequest;
use App\Models\Contract;
use App\Services\AdminControlService;
use Inertia\Inertia;

class AdminControlController extends Controller
{
    //

    public function renderView(AdminControlService $service)
    {
        return Inertia::render('AdminControl', [
            'notifications' => Inertia::once(fn() => $service->getNotifications()),
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

    public function updateContract(StoreContractRequest $request, Contract $contract, AdminControlService $service)
    {
        $validated = $request->validated();
        $dto = StoreContractDto::fromArray($validated);
        try {
            $service->updateContract($contract, $dto);

            Inertia::flash([
                'success' => 'Contrato actualizado exitosamente.',
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


    public function renew(Contract $contract, RenewContractRequest $request, AdminControlService $service)
    {
        $validated = $request->validated();
        $dto = RenewContractDto::fromArray($validated);

        try {
            $data = $service->renewContract($contract, $dto);

            Inertia::flash([
                'success' => 'Contrato renovado exitosamente.',
                'contract' => $data['contract'],
                'renewal' => $data['renewal'],
                'error' => null
            ]);
        } catch (\Exception $e) {

            Inertia::flash([
                'success' => null,
                'contract' => null,
                'renewal' => null,
                'error' => $e->getMessage()
            ]);
        }

        return back();
    }


    public function cancel(Contract $contract, AdminControlService $service)
    {
        try {
            $service->cancelContract($contract);

            Inertia::flash([
                'success' => 'Contrato cancelado exitosamente.',
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

    public function destroy(Contract $contract)
    {
        try {
            $contract->delete();

            Inertia::flash([
                'success' => 'Contrato eliminado exitosamente.',
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
