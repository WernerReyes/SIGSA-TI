<?php

namespace App\Services;

use App\DTOs\AdminControl\StoreContractDto;
use App\Enums\Contract\BillingFrequency;
use App\Enums\Contract\ContractPeriod;
use App\Enums\Contract\ContractStatus;
use App\Enums\notification\NotificationEntity;
use App\Models\Contract;
use App\Models\ContractBilling;
use App\Models\ContractExpiration;

use App\Models\Notification;
use Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class AdminControlService
{

    public function getNotifications()
    {
        return Notification::with('contract.billing', 'contract.expiration')->where('type', NotificationEntity::CONTRACT->value)->where('notifiable_id', Auth::id())->get();
    }

    public function getContracts()
    {
        return Contract::with('billing', 'expiration')->get();
    }
    public function storeContract(StoreContractDto $dto): Contract
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

                $this->storeOrUpdateBilling($contract, $dto);
                $this->storeOrUpdateExpiration($contract, $dto);

                return $contract;
            });
        } catch (\Throwable $e) {
            throw new InternalErrorException('Error al crear contrato: ' . $e->getMessage());
        }
    }

    public function updateContract(Contract $contract, StoreContractDto $dto): Contract
    {
        try {
            return DB::transaction(function () use ($contract, $dto) {

                $contract->update([
                    'name' => $dto->name,
                    'provider' => $dto->provider,
                    'type' => $dto->type,
                    'period' => $dto->period,
                    'status' => $dto->status,
                    'start_date' => $dto->startDate,
                    'end_date' => $dto->endDate,
                    'notes' => $dto->notes,
                ]);

                $this->storeOrUpdateBilling($contract, $dto);
                $this->storeOrUpdateExpiration($contract, $dto);

                return $contract;
            });
        } catch (\Throwable $e) {
            throw new InternalErrorException('Error al actualizar contrato: ' . $e->getMessage());
        }
    }

    /**
     * Billing logic
     */
    private function storeOrUpdateBilling(Contract $contract, StoreContractDto $dto): void
    {
        $isActive = $dto->status === ContractStatus::ACTIVE->value;

        $data = [
            'frequency' => $dto->frequency,
            'amount' => $dto->amount,
            'currency' => $dto->currency,
            'auto_renew' => $dto->autoRenew,
            'billing_cycle_days' => BillingFrequency::getDay($dto->frequency),
            'next_billing_date' => $dto->nextBillingDate,
            'is_active' => $isActive,
        ];

        if ($contract->billing) {
            $contract->billing->update($data);
        } else {
            $data['contract_id'] = $contract->id;
            ContractBilling::create($data);
        }
    }

    /**
     * Expiration logic
     */
    private function storeOrUpdateExpiration(Contract $contract, StoreContractDto $dto): void
    {
        // ❌ Si está cancelado, NO crear expiration
        if ($dto->status === ContractStatus::CANCELED->value) {
            if ($contract->expiration) {
                $contract->expiration->delete();
            }
            return;
        }

        $shouldHaveExpiration =
            $dto->period === ContractPeriod::FIXED_TERM->value ||
            $dto->period === ContractPeriod::ONE_TIME->value ||
            ($dto->period === ContractPeriod::RECURRING->value && !$dto->autoRenew);

        if (!$shouldHaveExpiration) {
            // Si cambia a recurrente auto-renew true, borrar expiration
            if ($contract->expiration) {
                $contract->expiration->delete();
            }
            return;
        }

        $expirationDate = $dto->period === ContractPeriod::RECURRING->value
            ? $dto->nextBillingDate
            : $dto->endDate;

        $data = [
            'expiration_date' => $expirationDate,
            'alert_days_before' => $dto->alertDaysBefore,
            'notified' => false,
        ];

        if ($contract->expiration) {
            if (!$contract->expiration->notified) {
                $contract->expiration->update($data);
            }
        } else {
            $data['contract_id'] = $contract->id;
            ContractExpiration::create($data);
        }
    }
}
