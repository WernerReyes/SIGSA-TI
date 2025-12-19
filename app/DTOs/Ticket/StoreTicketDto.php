<?php
class StoreTicketDto {
    private function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly int $eventId,
        public readonly int $userId,
    ) {}
}