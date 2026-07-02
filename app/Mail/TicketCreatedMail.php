<?php

namespace App\Mail;

use App\Enums\Ticket\TicketCategory;
use App\Enums\Ticket\TicketImpact;
use App\Enums\Ticket\TicketPriority;
use App\Enums\Ticket\TicketStatus;
use App\Enums\Ticket\TicketType;
use App\Enums\Ticket\TicketUrgency;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TicketCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nuevo ticket #{$this->ticket->id}: {$this->ticket->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-created',
            with: [
                'ticket' => $this->ticket,
                'typeLabel' => TicketType::label($this->ticket->type),
                'categoryLabel' => $this->ticket->category ? TicketCategory::label($this->ticket->category) : 'No aplica',
                // 'impactLabel' => TicketImpact::label($this->ticket->impact),
                // 'urgencyLabel' => TicketUrgency::label($this->ticket->urgency),
                'priorityLabel' => TicketPriority::label($this->ticket->priority),
                'statusLabel' => TicketStatus::label($this->ticket->status),
            ],
        );
    }

    public function attachments(): array
    {
        if (!is_array($this->ticket->images)) {
            return [];
        }

        $attachments = [];

        foreach ($this->ticket->images as $index => $imagePath) {
            if (!$imagePath || !Storage::disk('public')->exists($imagePath)) {
                continue;
            }

            $absolutePath = Storage::disk('public')->path($imagePath);
            $extension = pathinfo($absolutePath, PATHINFO_EXTENSION) ?: 'jpg';

            $attachments[] = Attachment::fromPath($absolutePath)
                ->as('ticket-' . $this->ticket->id . '-imagen-' . ($index + 1) . '.' . $extension)
                ->withMime(mime_content_type($absolutePath) ?: 'application/octet-stream');
        }

        return $attachments;
    }
}
