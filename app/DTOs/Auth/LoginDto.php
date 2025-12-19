<?php
namespace App\DTOs\Auth;
class LoginDto
{

    private function __construct(
        public readonly string $username,
        public readonly string $password,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['username'],
            $data['password'],
        );
    }
}