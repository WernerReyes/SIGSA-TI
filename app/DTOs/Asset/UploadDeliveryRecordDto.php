<?php
namespace App\DTOs\Asset;


class UploadDeliveryRecordDto
{
    private function __construct(
        public readonly string $file,
     
        public string $type,

    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            file: $data['file'],
          
            type: $data['type'],
        );
    }
}