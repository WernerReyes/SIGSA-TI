<?php
namespace App\DTOs\AdminControl;

use App\Enums\Contract\ContractPeriod;


class StoreContractDto
{
    private function __construct(
        public readonly string $name,
        public readonly string $provider,
        public readonly string $type,
        public readonly string $period,
        // public readonly string $status,
        public readonly string $startDate,
        public readonly ?string $endDate = null,
        public readonly ?string $notes,
        public readonly ?string $frequency,
        public readonly ?float $amount,
        public readonly ?string $currency,
        public readonly ?bool $autoRenew,
        public readonly ?string $nextBillingDate,
        public readonly ?int $alertDaysBefore
    ) {
    }

    public static function fromArray(array $data): self
    {   
        $amount = $data['amount'] ?? null;
      if ($data['period'] === ContractPeriod::RECURRING->value) {
            $data['end_date'] = null;
      } else if ($data['period'] === ContractPeriod::ONE_TIME->value) {
            $data['frequency'] = null;
            $data['auto_renew'] = false;
            $data['next_billing_date'] = null;
            $data['billing_cycle_days'] = null;
        } 

        return new self(
            name: $data['name'],
            provider: $data['provider'],
            type: $data['type'],
            period: $data['period'],
            // status: $data['status'],
            startDate: $data['start_date'],
            endDate: $data['end_date'] ?? null,
            notes: $data['notes'] ?? null,
            frequency: $data['frequency'] ?? null,
            amount: $amount !== null ? (float) $amount : null,
            currency: $data['currency'] ?? null,
            autoRenew: isset($data['auto_renew']) ? (bool) $data['auto_renew'] : null,
            nextBillingDate: $data['next_billing_date'] ?? null,
            alertDaysBefore: isset($data['alert_days_before']) ? (int) $data['alert_days_before'] : null,
        );
    }
}