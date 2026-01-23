<?php
namespace App\Services;

use App\DTOs\Ticket\StoreTicketDto;
use App\Enums\Ticket\TicketHistoryAction;
use App\Enums\Ticket\TicketPriority;
use App\Enums\Ticket\TicketRequestType;
use App\Enums\Ticket\TicketStatus;
use App\DTOs\Ticket\TicketFiltersDto;
use App\Enums\Ticket\TicketType;
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
            })->when($filters->startDate, function ($query) use ($filters) {
                $query->whereDate('created_at', '>=', $filters->startDate);
            })->when($filters->endDate, function ($query) use ($filters) {
                $query->whereDate('created_at', '<=', $filters->endDate);
            })
            ->orderedByPriority()
            ->paginate(10)
            ->withQueryString();
    }

    public function getHistoriesPaginated(Ticket $ticket)
    {
        return $ticket->histories()
            ->with('performedBy:staff_id,firstname,lastname')
            ->orderBy('performed_at', 'desc')
            ->paginate(10);
    }

    public function storeTicket(StoreTicketDto $dto)
    {
        try {
            DB::transaction(function () use ($dto) {
                $ticket = Ticket::create([
                    'title' => $dto->title,
                    'status' => TicketStatus::OPEN->value,
                    'description' => $dto->description,

                    'requester_id' => $dto->requesterId,
                    'type' => $dto->type->value,
                    'priority' => $dto->priority->value,
                    'request_type' => $dto->requestType?->value,
                    // 'opened_at' => now(),
                ]);



                $this->logHistory($ticket, TicketHistoryAction::CREATED, 'Ticket creado');


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

                $original = $ticket->getOriginal();

                $ticket->title = $dto->title;
                // $ticket->status = $dto->status->value;
                $ticket->description = $dto->description;
                $ticket->type = $dto->type->value;
                $ticket->priority = $dto->priority->value;
                $ticket->request_type = $dto->requestType?->value;
                $ticket->save();

                $changes = $ticket->getChanges();
                if (count($changes) > 0) {
                    $changeDetails = [];
                    foreach ($changes as $field => $newValue) {
                        $oldValue = $original[$field] ?? null;

                        if ($oldValue === $newValue || $field === 'updated_at') {
                            continue;
                        }

                        if ($field === 'request_type') {
                            if ($oldValue === null) {
                                $changeDetails[] = "{$this->fieldsLabels($field)} establecido a '" . TicketRequestType::label($newValue) . "'";
                            } elseif ($newValue === null) {
                                $changeDetails[] = "{$this->fieldsLabels($field)} eliminado (antes '" . TicketRequestType::label($oldValue) . "')";
                            }
                            continue;

                        }

                        switch ($field) {

                            case 'priority':
                                $oldValue = TicketPriority::label($oldValue);
                                $newValue = TicketPriority::label($newValue);
                                break;
                            case 'type':
                                $oldValue = TicketType::label($oldValue);
                                $newValue = TicketType::label($newValue);
                                break;
                            case 'request_type':
                                $oldValue = TicketRequestType::label($oldValue);
                                $newValue = TicketRequestType::label($newValue);
                                break;
                        }



                        $changeDetails[] = "{$this->fieldsLabels($field)} cambiado de '{$oldValue}' a '{$newValue}'";
                    }
                    $changeDescription = implode('; ', $changeDetails);

                    $this->logHistory($ticket, TicketHistoryAction::UPDATED, $changeDescription);
                }

                return $ticket;
            });


        } catch (\Exception $e) {
            throw new InternalErrorException("Error al actualizar el ticket: " . $e->getMessage());
        }
    }


    private function fieldsLabels(string $field): string
    {
        return match ($field) {
            'title' => 'Título',
            'status' => 'Estado',
            'description' => 'Descripción',
            'type' => 'Tipo',
            'priority' => 'Prioridad',
            'request_type' => 'Tipo de Solicitud',
            default => $field,
        };
    }

    private function logHistory(Ticket $ticket, TicketHistoryAction $action, ?string $description = null)
    {
        TicketHistory::create([
            'action' => $action->value,
            'ticket_id' => $ticket->id,
            'performed_by' => auth()->id(),
            'description' => $description,
            'performed_at' => now(),
        ]);
    }

    public function deleteTicket(Ticket $ticket)
    {
        $user = Auth::user();
        if ($ticket->requester_id !== $user->staff_id) {
            throw new BadRequestException("No tienes permiso para eliminar este ticket.");
        }

        if ($ticket->status !== TicketStatus::OPEN->value) {
            throw new BadRequestException("Solo se pueden eliminar los tickets en estado 'Abierto'.");
        }

        if ($ticket->responsible_id !== null) {
            throw new BadRequestException("No se puede eliminar un ticket que ya ha sido asignado.");
        }

        try {

            $ticket->delete();

        } catch (\Exception $e) {
            throw new InternalErrorException("Error al eliminar el ticket: " . $e->getMessage());
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

                $ticket->update([
                    'responsible_id' => $responsible_id,
                ]);

                $description = $oldResponsible
                    ? "Reasignado de responsable {$oldResponsible->full_name} a {$responsible->full_name}"
                    : "Asignado responsable {$responsible->full_name}";



                $this->logHistory($ticket, TicketHistoryAction::ASSIGNED, $description);

                return ['description' => $description, 'responsible' => $responsible];
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

                $description = $newStatus === TicketStatus::CLOSED->value
                    ? "Cerrado el ticket"
                    : "Cambio de estado de {TicketStatus::label($oldStatus)} a {TicketStatus::label($newStatus)}";

                $this->logHistory($ticket, TicketHistoryAction::STATUS_CHANGED, $description);

                return $description;
            });
        } catch (\Exception $e) {
            if ($e instanceof BadRequestException) {
                throw $e;
            }
            throw new InternalErrorException("Error al cambiar el estado del ticket: " . $e->getMessage());
        }
    }









}