<?php

namespace App\DTOs\Asset;

use Illuminate\Http\UploadedFile;

class SendDeliveryRecordEmailDto
{
    private function __construct(
        public readonly string $documentType,
        public readonly string $emailTo,
        public readonly ?string $emailCc,
        public readonly ?string $message,
        public readonly array $messageSections,
        /** @var UploadedFile[] */
        public readonly array $extraImages,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            documentType: $data['document_type'],
            emailTo: $data['email_to'],
            emailCc: $data['email_cc'] ?? null,
            message: $data['message'] ?? null,
            messageSections: $data['message_sections'] ?? [],
            extraImages: $data['extra_images'] ?? [],
        );
    }
}
