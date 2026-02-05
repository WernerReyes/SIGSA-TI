<?php
namespace App\Services;


use App\DTOs\AdminControl\StoreContractDto;
use App\Models\Contract;
use App\Models\ContractBilling;
use DB;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
class AdminControlService
{



    public function getContracts()
    {
        return Contract::with('billing', 'expiration')->get();
    }

    public function storeContract(StoreContractDto $dto)
    {


        try {
            return DB::transaction(function () use ($dto) {

                $contract = Contract::create([
                    'name' => $dto->name,
                    'provider' => $dto->provider,
                    'type' => $dto->type,
                    // 'total_amount' => $dto->totalAmount,
                    'period' => $dto->period,
                    'status' => $dto->status,
                    'start_date' => $dto->startDate,
                    'end_date' => $dto->endDate,
                    'notes' => $dto->notes,
                ]);


                ContractBilling::create([
                    'contract_id' => $contract->id,
                    'frequency' => $dto->frequency,
                    'amount' => $dto->amount,
                    'currency' => $dto->currency,
                    'auto_renew' => $dto->autoRenew,
                    'billing_cycle_days' => $dto->billingCycleDays,
                    'next_billing_date' => $dto->nextBillingDate,
                ]);

                return $contract;
            });

        } catch (\Exception $e) {
            throw new InternalErrorException('Error al crear el contrato: ' . $e->getMessage());
        }
    }
}

