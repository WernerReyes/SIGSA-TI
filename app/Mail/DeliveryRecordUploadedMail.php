<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Storage;

class DeliveryRecordUploadedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array<int, array{path: string, name: string}> $extraAttachments
     */
    public function __construct(
        public readonly string $recordTypeLabel,
        public readonly string $assetName,
        public readonly string $assignedToName,
        public readonly string $mainAttachmentPath,
        public readonly string $mainAttachmentName,
        public readonly array $extraAttachments = [],
        public readonly ?string $customMessage = null,
        public readonly array $messageSections = [],
        public readonly ?string $customSubject = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->customSubject ?: ('Constancia de ' . $this->recordTypeLabel . ' - ' . $this->assetName),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.delivery-record-uploaded',
            with: [
                'recordTypeLabel' => $this->recordTypeLabel,
                'assetName' => $this->assetName,
                'assignedToName' => $this->assignedToName,
                'customMessage' => $this->customMessage,
                'messageSections' => $this->messageSections,
                'mainAttachmentName' => $this->mainAttachmentName,
                'extraAttachmentNames' => array_values(array_map(fn(array $item) => $item['name'] ?? '', $this->extraAttachments)),
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if (file_exists($this->mainAttachmentPath)) {
            $attachments[] = Attachment::fromPath($this->mainAttachmentPath)
                ->as($this->mainAttachmentName)
                ->withMime(mime_content_type($this->mainAttachmentPath));
        }

        foreach ($this->extraAttachments as $attachment) {

            if (
                !isset($attachment['path']) ||
                !isset($attachment['name'])

            ) {
                continue;
            }

            $absolutePath = Storage::disk('public')->path($attachment['path']);

            if (!file_exists($absolutePath)) {
                continue;
            }

            $attachments[] = Attachment::fromPath($absolutePath)
                ->as($attachment['name'])
                ->withMime(mime_content_type($absolutePath));
        }

        return $attachments;
    }
}
