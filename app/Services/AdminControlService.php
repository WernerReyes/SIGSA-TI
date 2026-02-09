<?php

namespace App\Services;

use App\DTOs\AdminControl\RenewContractDto;
use App\DTOs\AdminControl\StoreContractDto;
use App\Enums\Contract\BillingFrequency;
use App\Enums\Contract\ContractPeriod;
use App\Enums\Contract\ContractStatus;
use App\Enums\Contract\ContractType;
use App\Enums\notification\NotificationEntity;
use App\Models\Contract;
use App\Models\ContractBilling;
use App\Models\ContractExpiration;

use App\Models\ContractRenewal;
use App\Models\Notification;
use Auth;
use Carbon\Carbon;
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
                    'status' => ContractStatus::ACTIVE->value,
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
                    // 'status' => $dto->status,
                    // 'start_date' => $dto->startDate,
                    // 'end_date' => $dto->endDate,
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
    private function storeOrUpdateBilling(Contract $contract, StoreContractDto|RenewContractDto $dto): void
    {
        // $isActive = $dto->status === ContractStatus::ACTIVE->value;

        $data = [
            'frequency' => $dto->frequency,
            'amount' => $dto->amount,
            'currency' => $dto->currency,
            'auto_renew' => $dto->autoRenew,
            'billing_cycle_days' => BillingFrequency::getDay($dto->frequency),
            'next_billing_date' => $dto->nextBillingDate,
            // 'is_active' => $isActive,
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
    private function storeOrUpdateExpiration(Contract $contract, StoreContractDto|RenewContractDto $dto): void
    {
        // Cancelado → borrar expiration
        // if ($dto->status === ContractStatus::CANCELED->value) {
        //     $contract->expiration?->delete();
        //     return;
        // }

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


    public function cancelContract(Contract $contract)
    {
        DB::transaction(function () use ($contract) {
            $contract->update([
                'status' => ContractStatus::CANCELED->value,
            ]);

            $contract->billing?->update(['auto_renew' => false]);
            $contract->expiration?->delete();
        });
    }



    public function renewContract(Contract $contract, RenewContractDto $dto)
    {
        if ($contract->status === ContractStatus::CANCELED->value) {
            throw new BadRequestException('El contrato está cancelado y no puede ser renovado.');
        }
        try {
            return DB::transaction(function () use ($contract, $dto) {

                $renewal = ContractRenewal::create([
                    'contract_id' => $contract->id,
                    'old_end_date' => $contract->billing->next_billing_date ?? $contract->end_date,
                    'new_end_date' => $dto->period === ContractPeriod::RECURRING->value ? $dto->nextBillingDate : $dto->endDate,
                    'renewed_by_id' => Auth::id(),
                    'notes' => $dto->notes,
                ]);

                $contract->update([
                    'period' => $dto->period,
                    'end_date' => $dto->period === ContractPeriod::RECURRING->value ? null : $dto->endDate,
                    'status' => ContractStatus::ACTIVE->value,
                    // 'notes' => $dto->notes,
                ]);

                $this->storeOrUpdateBilling($contract, $dto);
                $this->storeOrUpdateExpiration($contract, $dto);

                return [
                    'contract' => $contract->load('billing', 'expiration'),
                    'renewal' => $renewal->load('renewedBy:staff_id,firstname,lastname'),
                ];
            });
        } catch (\Throwable $e) {
            throw new InternalErrorException('Error al renovar contrato: ' . $e->getMessage());
        }
    }

    public function autoRenew(ContractBilling $billing)
    {
        $months = BillingFrequency::getMonth($billing->frequency);
        $nextDate = Carbon::parse($billing->next_billing_date)->addMonths($months);

        try {
            DB::transaction(function () use ($billing, $nextDate) {

                $label = ContractType::label($billing->contract->type);

                ContractRenewal::create([
                    'contract_id' => $billing->contract->id,
                    'old_end_date' => $billing->next_billing_date,
                    'new_end_date' => $nextDate->toDateString(),
                    'renewed_by_id' => Auth::id(),
                    'notes' => "{$label}: {$billing->contract->name} se renovó automáticamente",
                ]);


                // Actualizar contrato
                $billing->contract->update([
                    'end_date' => null,
                    'status' => ContractStatus::ACTIVE->value,
                ]);


                $billing->update([
                    'next_billing_date' => $nextDate->toDateString(),
                ]);

            });


        } catch (\Exception $e) {

            throw new InternalErrorException('Error al renovar contrato: ' . $e->getMessage());
        }

    }


    

}
