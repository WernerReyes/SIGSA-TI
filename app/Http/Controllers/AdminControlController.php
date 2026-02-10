<?php

namespace App\Http\Controllers;


use App\DTOs\AdminControl\RenewContractDto;
use App\DTOs\AdminControl\StoreContractDto;
use App\DTOs\AdminControl\StoreInfrastructureEventDto;
use App\Http\Requests\AdminControl\RenewContractRequest;
use App\Http\Requests\AdminControl\StoreContractRequest;
use App\Http\Requests\AdminControl\StoreInfrastructureEventRequest;
use App\Models\Contract;
use App\Models\InfrastructureEvents;
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
            'infrastructureEvents' => Inertia::once(fn() => $service->getInfrastructureEvents()),
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

    //* Infrastructure Events *//
    public function storeInfrastructureEvent(StoreInfrastructureEventRequest $request, AdminControlService $service)
    {
        $validated = $request->validated();
        $dto = StoreInfrastructureEventDto::fromArray($validated);

        try {
            $event = $service->storeInfrastructureEvent($dto);

            Inertia::flash([
                'success' => 'Evento de infraestructura creado exitosamente.',
                'event' => $event,
                'error' => null
            ]);
        } catch (\Exception $e) {

            Inertia::flash([
                'success' => null,
                'event' => null,
                'error' => $e->getMessage()
            ]);
        }

        return back();
    }


    public function updateInfrastructureEvent(InfrastructureEvents $event, StoreInfrastructureEventRequest $request, AdminControlService $service)
    {
        $validated = $request->validated();
        $dto = StoreInfrastructureEventDto::fromArray($validated);

        try {
            $service->updateInfrastructureEvent($event, $dto);

            Inertia::flash([
                'success' => 'Evento de infraestructura actualizado exitosamente.',
                'event' => $event,
                'error' => null
            ]);
        } catch (\Exception $e) {

            Inertia::flash([
                'success' => null,
                'event' => null,
                'error' => $e->getMessage()
            ]);
        }

        return back();
    }

    public function destroyInfrastructureEvent(InfrastructureEvents $event, AdminControlService $service)
    {
        try {
            $service->deleteInfrastructureEvent($event);

            Inertia::flash([
                'success' => 'Evento de infraestructura eliminado exitosamente.',
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
