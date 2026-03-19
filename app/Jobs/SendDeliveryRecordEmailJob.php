<?php

namespace App\Jobs;

use App\Mail\DeliveryRecordUploadedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendDeliveryRecordEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param array<int, string> $toEmails
     * @param array<int, string> $ccEmails
     * @param array<int, array{path: string, name: string}> $extraAttachments
     * @param array<string, mixed> $messageSections
     * @param array<int, string> $extraImageNames
     */
    public function __construct(
        public readonly int $assignmentId,
        public readonly int $deliveryRecordId,
        public readonly int $sentBy,
        public readonly string $documentType,
        public readonly array $toEmails,
        public readonly array $ccEmails,
        public readonly string $recordTypeLabel,
        public readonly string $assetName,
        public readonly string $assignedToName,
        public readonly string $mainAttachmentPath,
        public readonly string $mainAttachmentName,
        public readonly array $extraAttachments,
        public readonly string $subject,
        public readonly string $messageForLog,
        public readonly array $messageSections,
        public readonly string $documentPath,
        public readonly array $extraImageNames,
    ) {
    }

    public function handle(): void
    {
        $extraAttachmentsForMail = [];

        foreach ($this->extraAttachments as $attachment) {
            $relativePath = $attachment['path'] ?? null;
            $name = $attachment['name'] ?? null;

            if (!$relativePath || !$name) {
                continue;
            }

            $absolutePath = Storage::disk('public')->path($relativePath);
            if (!file_exists($absolutePath)) {
                continue;
            }

            $extraAttachmentsForMail[] = [
                'path' => $absolutePath,
                'name' => $name,
            ];
        }


        Mail::to($this->toEmails)
            ->cc($this->ccEmails)
            ->send(new DeliveryRecordUploadedMail(
                recordTypeLabel: $this->recordTypeLabel,
                assetName: $this->assetName,
                assignedToName: $this->assignedToName,
                mainAttachmentPath: $this->mainAttachmentPath,
                mainAttachmentName: $this->mainAttachmentName,
                extraAttachments: $extraAttachmentsForMail,
                customMessage: $this->messageForLog,
                messageSections: $this->messageSections,
                customSubject: $this->subject,
            ));




        // Limpia archivos temporales de adjuntos extra luego del envío.
        foreach ($this->extraAttachments as $attachment) {
            $relativePath = $attachment['path'] ?? null;
            if ($relativePath && Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        }
    }
}
