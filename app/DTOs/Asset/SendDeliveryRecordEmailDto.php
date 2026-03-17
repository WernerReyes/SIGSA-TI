<?php

namespace App\DTOs\Asset;

use Illuminate\Http\UploadedFile;

class SendDeliveryRecordEmailDto
{
    private function __construct(
        public readonly string $documentType,
        public readonly string $emailTo,
        public readonly string $message,
        /** @var UploadedFile[] */
        public readonly array $extraImages,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            documentType: $data['document_type'],
            emailTo: $data['email_to'],
            message: $data['message'],
            extraImages: $data['extra_images'] ?? [],
        );
    }
}
