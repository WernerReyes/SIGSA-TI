<?php

namespace App\Services;

use App\DTOs\AdminControl\RenewContractDto;
use App\DTOs\AdminControl\StoreContractDto;
use App\Enums\Contract\BillingFrequency;
use App\Enums\Contract\ContractPeriod;
use App\Enums\Contract\ContractStatus;
use App\Enums\notification\NotificationEntity;
use App\Models\Contract;
use App\Models\ContractBilling;
use App\Models\ContractExpiration;

use App\Models\ContractRenewal;
use App\Models\Notification;
use Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AdminControlService
{

    public function getNotifications()
    {
        return Notification::with('contract.billing', 'contract.expiration')
            ->where('type', NotificationEntity::CONTRACT->value)
            ->where('notifiable_id', Auth::id())->orderBy('created_at', 'desc')->
            get();
    }

    public function getContracts()
    {
        return Contract::with('billing', 'expiration', 'renewals.renewedBy')->get();
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

                return $contract->load('billing', 'expiration');
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

                return $contract->load('billing', 'expiration');
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
        // Cancelado → borrar expiration
        if ($dto->status === ContractStatus::CANCELED->value) {
            $contract->expiration?->delete();
            return;
        }

        $shouldHaveExpiration =
            $dto->period === ContractPeriod::FIXED_TERM->value ||
            ($dto->period === ContractPeriod::ONE_TIME->value && $dto->endDate) ||
            ($dto->period === ContractPeriod::RECURRING->value && !$dto->autoRenew);

        if (!$shouldHaveExpiration) {
            $contract->expiration?->delete();
            return;
        }

        $expirationDate = $dto->period === ContractPeriod::RECURRING->value
            ? $dto->nextBillingDate
            : $dto->endDate;

        $data = [
            'expiration_date' => $expirationDate,
            'alert_days_before' => $dto->alertDaysBefore,
        ];

        if ($contract->expiration) {

            // Detectar cambios críticos
            $hasChanged =
                $contract->expiration->expiration_date != $expirationDate ||
                $contract->expiration->alert_days_before != $dto->alertDaysBefore;

            if ($hasChanged) {
                $data['notified'] = false; // RESET
            }

            $contract->expiration->update($data);

        } else {

            $data['contract_id'] = $contract->id;
            $data['notified'] = false;

            ContractExpiration::create($data);
        }
    }


    public function renewContract(Contract $contract, RenewContractDto $dto)
    {
        if ($contract->status === ContractStatus::ACTIVE->value && !$dto->autoRenew) {
        throw new BadRequestException('El contrato ya está activo. Solo se pueden renovar contratos vencidos o por vencer.');
    }
        try {
        return DB::transaction(function () use ($contract, $dto) {

            // Guardar historial
            ContractRenewal::create([
                'contract_id' => $contract->id,
                'old_end_date' => $contract->billing->next_billing_date ?? $contract->end_date,
                'new_end_date' => $dto->newEndDate,
                'renewed_by_id' => Auth::id(),
                'notes' => $dto->notes,
            ]);

            // Actualizar contrato
            $contract->update([
                'end_date' =>  $dto->autoRenew ? null : $dto->newEndDate,
                'status' => ContractStatus::ACTIVE->value,
            ]);

            if ($dto->autoRenew) {
                $contract->billing->update([
                    'next_billing_date' => $dto->newEndDate,
                ]);
                return;
            }

            // Reset expiration alert
            if ($contract->expiration) {
                $contract->expiration->update([
                    'expiration_date' => $dto->newEndDate,
                    'alert_days_before' => $dto->alertDaysBefore,
                    'notified' => false,
                ]);
            } else {
                ContractExpiration::create([
                    'contract_id' => $contract->id,
                    'expiration_date' => $dto->newEndDate,
                    'alert_days_before' => $dto->alertDaysBefore,
                    'notified' => false,
                ]);
            }

          
        });
    } catch (\Exception $e) {
        ds($e->getMessage());
        throw new InternalErrorException('Error al renovar contrato: ' . $e->getMessage());
    }
    }


}
