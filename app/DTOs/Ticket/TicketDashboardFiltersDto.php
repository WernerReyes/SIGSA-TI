<?php

namespace App\DTOs\Ticket;

use Carbon\CarbonImmutable;

class TicketDashboardFiltersDto
{
    private function __construct(
        public readonly ?string $startDate,
        public readonly ?string $endDate,
        public readonly ?array $responsibleIds,
        public readonly ?array $requesterIds,
        public readonly ?array $statuses,
        public readonly ?array $types,
        public readonly ?array $categories,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            startDate: isset($data['start_date'])
                ? CarbonImmutable::parse($data['start_date'])->toDateString()
                : null,
            endDate: isset($data['end_date'])
                ? CarbonImmutable::parse($data['end_date'])->toDateString()
                : null,
            responsibleIds: self::normalizeIntegerArray($data, 'responsible_ids', 'responsible_id'),
            requesterIds: self::normalizeIntegerArray($data, 'requester_ids', 'requester_id'),
            statuses: self::normalizeStringArray($data, 'statuses', 'status'),
            types: self::normalizeStringArray($data, 'types', 'type'),
            categories: self::normalizeStringArray($data, 'categories', 'category'),
        );
    }

    public function toArray(): array
    {
        return [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'responsible_ids' => $this->responsibleIds,
            'requester_ids' => $this->requesterIds,
            'statuses' => $this->statuses,
            'types' => $this->types,
            'categories' => $this->categories,
        ];
    }

    private static function normalizeIntegerArray(array $data, string $pluralKey, string $singularKey): ?array
    {
        $values = self::valuesFrom($data, $pluralKey, $singularKey);

        if ($values === null) {
            return null;
        }

        return array_values(array_unique(array_map('intval', $values)));
    }

    private static function normalizeStringArray(array $data, string $pluralKey, string $singularKey): ?array
    {
        $values = self::valuesFrom($data, $pluralKey, $singularKey);

        if ($values === null) {
            return null;
        }

        return array_values(array_unique(array_map('strval', $values)));
    }

    private static function valuesFrom(array $data, string $pluralKey, string $singularKey): ?array
    {
        $value = $data[$pluralKey] ?? $data[$singularKey] ?? null;

        if ($value === null || $value === '' || $value === []) {
            return null;
        }

        return array_values(array_filter(
            is_array($value) ? $value : [$value],
            fn ($item) => $item !== null && $item !== '',
        )) ?: null;
    }
}
