<?php
namespace App\Services;

use App\DTOs\Ticket\StoreTicketDto;
use App\Enums\Ticket\TicketStatus;
use App\DTOs\Ticket\TicketFiltersDto;
use App\Models\Ticket;
use App\Models\TicketHistory;
use App\Models\User;
use Auth;
use DB;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TicketService
{


    public function getAllOrderedByPriority(TicketFiltersDto $filters)
    {

        ds($filters);
        return Ticket::
            query()
            ->select([
                'id',
                'title',
                'description',
                'status',
                'priority',
                'type',
                'request_type',
                'requester_id',
                'responsible_id',
                'created_at',
                'updated_at',
            ])
            ->with([
                'requester:staff_id,firstname,lastname,dept_id',
                'requester.department:id,name',
                'responsible:staff_id,firstname,lastname',
            ])->
            when($filters->searchTerm, function ($query) use ($filters) {
                $query->where('title', 'LIKE', '%' . $filters->searchTerm . '%')
                    ->orWhere('description', 'LIKE', '%' . $filters->searchTerm . '%');
            })->when($filters->requesters, function ($query) use ($filters) {
                $query->whereIn('requester_id', $filters->requesters);
            })->when($filters->responsibles, function ($query) use ($filters) {
                if (in_array(null, $filters->responsibles, true)) {
                    ds('includes null');
                    $query->where(function ($subQuery) use ($filters) {
                        $subQuery->whereNull('responsible_id')
                            ->orWhereIn('responsible_id', array_filter($filters->responsibles));
                    });
                } else {
                    $query->whereIn('responsible_id', $filters->responsibles);
                }

            })->when($filters->statuses, function ($query) use ($filters) {
                $query->whereIn('status', $filters->statuses);
            })->when($filters->types, function ($query) use ($filters) {
                $query->whereIn('type', $filters->types);
            })->when($filters->priorities, function ($query) use ($filters) {
                $query->whereIn('priority', $filters->priorities);
            })
            ->orderedByPriority()
            ->paginate(10)
            ->withQueryString();
    }

    public function storeTicket(StoreTicketDto $dto)
    {
        try {
            DB::transaction(function () use ($dto) {
                $ticket = Ticket::create([
                    'title' => $dto->title,
                    'status' => $dto->status->value,
                    'description' => $dto->description,

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
        } catch (\Exception $e) {
            throw new InternalErrorException("Error al crear el ticket: " . $e->getMessage());
        }


    }

    public function updateTicket(Ticket $ticket, StoreTicketDto $dto)
    {

        $user = Auth::user();
        if ($ticket->requester_id !== $user->staff_id) {
            throw new BadRequestException("No tienes permiso para actualizar este ticket.");
        }

        if ($ticket->status !== TicketStatus::OPEN->value) {
            throw new BadRequestException("Solo se pueden actualizar los tickets en estado 'Abierto'.");
        }


        if ($ticket->responsible_id !== null) {
            throw new BadRequestException("No se puede actualizar un ticket que ya ha sido asignado.");
        }

        try {
            return DB::transaction(function () use ($ticket, $dto) {
                $ticket->update([
                    'title' => $dto->title,
                    'status' => $dto->status->value,
                    'description' => $dto->description,
                    // 'technician_id' => $dto->technicianId,
                    'type' => $dto->type->value,
                    'priority' => $dto->priority->value,
                    'request_type' => $dto->requestType?->value,
                ]);

                TicketHistory::create([
                    'action' => 'Ticket Actualizado',
                    'ticket_id' => $ticket->id,
                    'performed_by' => auth()->id(),
                    'performed_at' => now(),
                ]);

                return $ticket;
            });


        } catch (\Exception $e) {
            throw new InternalErrorException("Error al actualizar el ticket: " . $e->getMessage());
        }
    }

    public function assignTicket(Ticket $ticket, int $responsible_id)
    {
        try {

            $responsible = User::select(
                'staff_id',
                'firstname',
                'lastname'
            )->find($responsible_id);
            if (!$responsible) {
                throw new NotFoundHttpException("Técnico no encontrado.");
            }

            return DB::transaction(function () use ($ticket, $responsible_id, $responsible) {
                // $ticket = Ticket::findOrFail($ticketId);
                $oldResponsible = $ticket->responsible;


                if ($oldResponsible && $oldResponsible->staff_id === $responsible_id) {
                    throw new BadRequestException("El ticket ya está asignado a este usuario de TI.");
                }

                $ticket->responsible_id = $responsible_id;
                $ticket->save();

                $action = $oldResponsible
                    ? "Reasignado de responsable {$oldResponsible->full_name} a {$responsible->full_name}"
                    : "Asignado responsable {$responsible->full_name}";

                TicketHistory::create([
                    'action' => $action,
                    'ticket_id' => $ticket->id,
                    'performed_by' => auth()->id(),
                    'performed_at' => now(),
                ]);

                return ['action' => $action, 'responsible' => $responsible];
            });
        } catch (\Exception $e) {
            if ($e instanceof NotFoundHttpException || $e instanceof BadRequestException) {
                throw $e;
            }
            throw new InternalErrorException("Error al asignar el responsable: " . $e->getMessage());
        }
    }




    public function changeTicketStatus(Ticket $ticket, string $newStatus)
    {
        try {
            $oldStatus = $ticket->status;

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
        } catch (\Exception $e) {
            if ($e instanceof BadRequestException) {
                throw $e;
            }
            throw new InternalErrorException("Error al cambiar el estado del ticket: " . $e->getMessage());
        }
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