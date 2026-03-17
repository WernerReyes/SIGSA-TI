<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

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
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $attachments = [
            Attachment::fromPath($this->mainAttachmentPath)->as($this->mainAttachmentName),
        ];

        foreach ($this->extraAttachments as $attachment) {
            if (!isset($attachment['path']) || !isset($attachment['name'])) {
                continue;
            }

            $attachments[] = Attachment::fromPath($attachment['path'])->as($attachment['name']);
        }

        return $attachments;
    }
}
