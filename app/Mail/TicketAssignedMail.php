<?php

namespace App\Mail;

use App\Enums\Ticket\TicketCategory;
use App\Enums\Ticket\TicketPriority;
use App\Enums\Ticket\TicketStatus;
use App\Enums\Ticket\TicketType;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly User $responsible,
        public readonly User $assignedBy,
        public readonly ?User $previousResponsible = null,
    ) {}

    public function envelope(): Envelope
    {
        $action = $this->previousResponsible ? 'Reasignación' : 'Asignación';

        return new Envelope(
            subject: "{$action} del ticket #{$this->ticket->id}: {$this->ticket->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-assigned',
            with: [
                'ticket' => $this->ticket,
                'responsible' => $this->responsible,
                'assignedBy' => $this->assignedBy,
                'previousResponsible' => $this->previousResponsible,
                'isReassignment' => $this->previousResponsible !== null,
                'typeLabel' => TicketType::label($this->ticket->type),
                'categoryLabel' => $this->ticket->category ? TicketCategory::label($this->ticket->category) : 'No aplica',
                'priorityLabel' => TicketPriority::label($this->ticket->priority),
                'statusLabel' => TicketStatus::label($this->ticket->status),
                'ticketUrl' => rtrim((string) config('app.url'), '/').'/tickets?ticket_id='.$this->ticket->id,
            ],
        );
    }
}
