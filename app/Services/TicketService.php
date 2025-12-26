<?php
namespace App\Services;

use App\DTOs\Ticket\StoreTicketDto;
use App\Enums\Ticket\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketHistory;
use App\Models\User;
use DB;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TicketService
{


    public function getAllOrderedByPriority()
    {
        return Ticket::orderedByPriority()->with([
            'requester:staff_id,firstname,lastname',
            'technician:staff_id,firstname,lastname',
            'histories.performer:staff_id,firstname,lastname'
        ])->get();
    }

    public function storeTicket(StoreTicketDto $dto)
    {

        DB::transaction(function () use ($dto) {
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

            TicketHistory::create([
                'action' => 'Ticket Creado',
                'ticket_id' => $ticket->id,
                'performed_by' => $dto->requesterId,
                'performed_at' => now(),
            ]);
        });
    }

    public function reassignTicket(int $ticketId, int $newTechnicianId)
    {
        $tech = User::findOrFail($newTechnicianId);
        if (!$tech) {
            throw new NotFoundHttpException("Técnico no encontrado.");
        }

        return DB::transaction(function () use ($ticketId, $newTechnicianId, $tech) {
            $ticket = Ticket::findOrFail($ticketId);
            $oldTechnician = $ticket->technician;


            if ($oldTechnician && $oldTechnician->staff_id === $newTechnicianId) {
                throw new BadRequestException("El ticket ya está asignado al técnico seleccionado.");
            }

            $ticket->technician_id = $newTechnicianId;
            $ticket->save();

            $action = $oldTechnician
                ? "Reasignado de técnico {$oldTechnician->full_name} a {$tech->full_name}"
                : "Asignado técnico {$tech->full_name}";

            TicketHistory::create([
                'action' => $action,
                'ticket_id' => $ticket->id,
                'performed_by' => auth()->id(),
                'performed_at' => now(),
            ]);

            return $action;
        });
    }


    public function changeTicketStatus(int $ticketId, string $newStatus)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $oldStatus = $ticket->status->value;

        if ($oldStatus === $newStatus) {
            throw new BadRequestException("El ticket ya tiene el estado seleccionado.");
        }

        return DB::transaction(function () use ($ticket, $newStatus, $oldStatus) {
            $ticket->status = $newStatus;
            if ($newStatus === TicketStatus::CLOSED->value) {
                $ticket->closed_at = now();
            }
            $ticket->save();

            $action = $newStatus === TicketStatus::CLOSED->value
                ? "Cerrado el ticket"
                : "Cambio de estado de {$this->getStatusLabel($oldStatus)} a {$this->getStatusLabel($newStatus)}";

            TicketHistory::create([
                'action' => $action,
                'ticket_id' => $ticket->id,
                'performed_by' => auth()->id(),
                'performed_at' => now(),
            ]);

            return $action;
        });
    }


    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            TicketStatus::OPEN->value => 'Abierto',
            TicketStatus::IN_PROGRESS->value => 'En Progreso',
            TicketStatus::RESOLVED->value => 'Resuelto',
            TicketStatus::CLOSED->value => 'Cerrado',
            default => 'Desconocido',
        };
    }



}