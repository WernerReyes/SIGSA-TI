<?php
namespace App\DTOs\Asset;

use Illuminate\Http\UploadedFile;


class UploadDeliveryRecordDto
{
    private function __construct(
        public readonly UploadedFile $file,
        public string $type,
        public readonly bool $sendEmail,
        public readonly ?string $emailTo,
        /** @var UploadedFile[] */
        public readonly array $extraImages,

    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            file: $data['file'],
            type: $data['type'],
            sendEmail: (bool) ($data['send_email'] ?? false),
            emailTo: $data['email_to'] ?? null,
            extraImages: $data['extra_images'] ?? [],
        );
    }
}