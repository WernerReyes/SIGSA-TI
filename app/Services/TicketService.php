<?php
namespace App\Services;

use App\DTOs\Ticket\StoreTicketDto;
use App\Models\Ticket;

class TicketService
{


    public function getAllOrderedByPriority()
    {
        return Ticket::orderedByPriority()->with(['requester', 'technician'])->get();
    }

    public function storeTicket(StoreTicketDto $dto)
    {
        $ticket = Ticket::create([
            'title' => $dto->title,
            'status' => $dto->status->value,
            'description' => $dto->description,
            'technician_id' => $dto->technicianId,
            'requester_id' => $dto->requesterId,
            'type' => $dto->type->value,
            'priority' => $dto->priority->value,
            'request_type' => $dto->requestType?->value,
            'opened_at' => now(),
        ]);

        return $ticket;


    }
}