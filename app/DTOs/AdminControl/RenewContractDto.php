<?php
namespace App\DTOs\AdminControl;
class RenewContractDto
{
    function __construct(
        public string $newEndDate,
        public ?int $alertDaysBefore,
        public ?string $notes,
        public ?bool $autoRenew = false,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            newEndDate: $data['new_end_date'],
            alertDaysBefore: $data['alert_days_before'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

}
