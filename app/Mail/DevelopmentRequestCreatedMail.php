<?php

namespace App\Mail;

use App\Enums\DevelopmentRequest\DevelopmentRequestPriority;
use App\Enums\DevelopmentRequest\DevelopmentRequestStatus;
use App\Enums\DevelopmentRequest\DevelopmentRequestType;
use App\Models\DevelopmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DevelopmentRequestCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly DevelopmentRequest $developmentRequest,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nueva solicitud de desarrollo #{$this->developmentRequest->id}: {$this->developmentRequest->title}",
        );
    }

    public function content(): Content
    {
        $statusLabels = DevelopmentRequestStatus::labels();

        return new Content(
            view: 'emails.development-request-created',
            with: [
                'developmentRequest' => $this->developmentRequest,
                'typeLabel' => DevelopmentRequestType::label($this->developmentRequest->type),
                'priorityLabel' => DevelopmentRequestPriority::label($this->developmentRequest->priority),
                'statusLabel' => $statusLabels[$this->developmentRequest->status] ?? $this->developmentRequest->status,
                'developmentUrl' => rtrim((string) config('app.url'), '/') . '/developments?development_request_id=' . $this->developmentRequest->id,
            ],
        );
    }

    public function attachments(): array
    {
        $path = $this->developmentRequest->requirement_path;

        if (!$path || !Storage::disk('public')->exists($path)) {
            return [];
        }

        $absolutePath = Storage::disk('public')->path($path);
        $extension = pathinfo($absolutePath, PATHINFO_EXTENSION) ?: 'pdf';

        return [
            Attachment::fromPath($absolutePath)
                ->as('solicitud-desarrollo-' . $this->developmentRequest->id . '-requerimiento.' . $extension)
                ->withMime(mime_content_type($absolutePath) ?: 'application/octet-stream'),
        ];
    }
}
