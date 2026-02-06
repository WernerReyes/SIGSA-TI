<?php
namespace App\Services;


use App\DTOs\AdminControl\StoreContractDto;
use App\Enums\Contract\BillingFrequency;
use App\Enums\Contract\ContractPeriod;
use App\Models\Contract;
use App\Models\ContractBilling;
use App\Models\ContractExpiration;
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
                    'period' => $dto->period,
                    'status' => $dto->status,
                    'start_date' => $dto->startDate,
                    'end_date' => $dto->endDate,
                    'notes' => $dto->notes,
                ]);

                $billing = ContractBilling::create([
                    'contract_id' => $contract->id,
                    'frequency' => $dto->frequency,
                    'amount' => $dto->amount,
                    'currency' => $dto->currency,
                    'auto_renew' => $dto->autoRenew,
                    'billing_cycle_days' => BillingFrequency::getDay($dto->frequency),
                    'next_billing_date' => $dto->nextBillingDate,
                ]);

                // 👉 SOLO registrar expiraciones reales
                ds($dto);
                if (
                    $dto->period === ContractPeriod::FIXED_TERM->value ||
                    $dto->period === ContractPeriod::ONE_TIME->value ||
                    (
                        $dto->period === ContractPeriod::RECURRING->value &&
                        $dto->autoRenew === false
                    )
                ) {
                    $expirationDate = $dto->period === ContractPeriod::RECURRING->value
                        ? $dto->nextBillingDate
                        : $dto->endDate;
                    ContractExpiration::create([
                        'contract_id' => $contract->id,
                        'expiration_type' => 'contrato',
                        'expiration_date' => $expirationDate,
                        'alert_days_before' => $dto->alertDaysBefore,
                        'notified' => false,
                    ]);
                }

                return $contract;
            });


        } catch (\Exception $e) {
            throw new InternalErrorException('Error al crear el contrato: ' . $e->getMessage());
        }
    }
}

