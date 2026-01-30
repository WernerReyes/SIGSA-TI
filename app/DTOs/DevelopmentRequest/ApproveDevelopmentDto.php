<?php
namespace App\DTOs\DevelopmentRequest;

use App\Models\User;
use Auth;
class ApproveDevelopmentDto
{
    private function __construct(
        public readonly User $approvedBy,
        public readonly string $status,
        // public readonly string $level,
        public readonly ?string $comments = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            approvedBy: Auth::user(),
            status: $data['status'],
            // level: $data['level'],
            comments: $data['comments'] ?? null,
        );
    }
}