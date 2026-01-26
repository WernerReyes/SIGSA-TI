<?php
namespace App\DTOs\Service;
class StoreServiceDto
{
    private function __construct(
        public readonly string $name,
        public readonly string $url,
        public readonly ?string $description,
        public readonly string $username,
        public readonly string $password,
        public readonly bool $is_active,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            url: $data['url'],
            description: $data['description'] ?? null,
            username: $data['username'],
            password: $data['password'],
            is_active: $data['is_active'],
        );
    }
}