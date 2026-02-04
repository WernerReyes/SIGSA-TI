<?php
namespace App\Services;


use App\DTOs\AdminControl\StoreContractDto;
use App\Models\Contract;
use App\Models\ContractBilling;
use DB;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
class AdminControlService
{
 
// TODO: Check why it throw an error
public function storeContract(StoreContractDto $dto) {
        try {
            DB::transaction(function() use ($dto) {
    
                $contract =Contract::create([
                    'name' => $dto->name,
                    'provider' => $dto->provider,
                    'type' => $dto->type,
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
    
             });

        } catch (\Exception $e) {
            throw new InternalErrorException('Error al crear el contrato: ' . $e->getMessage());
        }
    }
}

