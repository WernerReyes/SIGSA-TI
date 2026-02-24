<?php
namespace App\Services;

use App\DTOs\Ticket\StoreTicketDto;
use App\DTOs\Ticket\TicketHistoryFiltersDto;
use App\Enums\Ticket\TicketCategory;
use App\Enums\Ticket\TicketHistoryAction;
use App\Enums\Ticket\TicketImpact;
use App\Enums\Ticket\TicketPriority;
use App\Enums\Ticket\TicketRequestType;
use App\Enums\Ticket\TicketStatus;
use App\DTOs\Ticket\TicketFiltersDto;
use App\Enums\Ticket\TicketType;
use App\Enums\Ticket\TicketUrgency;
use App\Enums\TicketAsset\TicketAssetAction;
use App\Models\AssetAssignment;
use App\Models\SlaPolicy;
use App\Models\Ticket;
use App\Models\TicketAsset;
use App\Models\TicketHistory;
use App\Models\User;
use App\Utils\CompressImage;
use Auth;
use Carbon\Carbon;
use DB;
use Storage;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TicketService
{

    private BusinessHoursService $businessHoursService;

    public function __construct(BusinessHoursService $businessHoursService)
    {
        $this->businessHoursService = $businessHoursService;
    }

    public function getAllOrderedByPriority(TicketFiltersDto $filters)
    {
        $list = Ticket::
            query()
            ->select([
                'id',
                'title',
                'description',
                'status',
                'impact',
                'urgency',
                'priority',
                'type',
                'category',
                'requester_id',
                'responsible_id',
                'images',
                'sla_response_due_at',
                'sla_resolution_due_at',
        

                'sla_paused_at',
                'sla_paused_duration',

                'first_response_at',
                'resolved_at',
                'sla_breached',
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
            })->when($filters->categories, function ($query) use ($filters) {
                if (in_array(null, $filters->categories, true)) {
                    $query->where(function ($subQuery) use ($filters) {
                        $subQuery->whereNull('category')
                            ->orWhereIn('category', array_filter($filters->categories));
                    });
                } else {
                    $query->whereIn('category', $filters->categories);
                }
            })
            ->when($filters->startDate, function ($query) use ($filters) {
                $query->whereDate('created_at', '>=', $filters->startDate);
            })->when($filters->endDate, function ($query) use ($filters) {
                $query->whereDate('created_at', '<=', $filters->endDate);
            })
            ->orderedByPriority()
            ->paginate(10)
            ->withQueryString();

            return $list;
    }

    public function getHistoriesPaginated(Ticket $ticket, TicketHistoryFiltersDto $filters)
    {
        return $ticket->histories()
            ->with('performer:staff_id,firstname,lastname')
            ->when($filters->actions, function ($query) use ($filters) {
                $query->whereIn('action', $filters->actions);
            })->when($filters->start_date, function ($query) use ($filters) {
                $query->whereDate('performed_at', '>=', $filters->start_date);
            })->when($filters->end_date, function ($query) use ($filters) {
                $query->whereDate('performed_at', '<=', $filters->end_date);
            })
            ->orderBy('performed_at', 'desc')
            ->paginate(10);
    }


    public function getCurrentAssignment(int $requester_id)
    {
        return AssetAssignment::with('asset', 'asset.type:id,name', 'childrenAssignments.asset.type:id,name', )
            ->where('assigned_to_id', $requester_id)
            ->whereNull('returned_at')
            ->latest('created_at')
            ->first();

    }

    public function getAssignmentsByRequester(int $requester_id)
    {
        return AssetAssignment::with(
            'asset:id,name,type_id,brand,model,serial_number',
            'asset.type:id,name',
            'childrenAssignments.asset:id,name,type_id',
            'childrenAssignments.asset.type:id,name'
        )
            ->where('assigned_to_id', $requester_id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getTicketById(int $id)
    {
        return Ticket::with([
            'requester:staff_id,firstname,lastname,dept_id',
            'requester.department:id,name',
            'responsible:staff_id,firstname,lastname',
        ])->findOrFail($id);
    }


    public function storeTicket(StoreTicketDto $dto)
    {

        $images = [];

        try {
            DB::transaction(function () use ($dto, &$images) {
                $sla = SlaPolicy::where('priority', $dto->priority)
                    ->first();

                if ($dto->images) {
                    foreach ($dto->images as $image) {
                        // $path = Storage::disk('public')->putFile('ticket_images', $image);
                        $path = CompressImage::save($image, 'ticket_images');
                        $images[] = $path;
                    }
                }

                $ticket = Ticket::create([
                    'title' => $dto->title,
                    'status' => TicketStatus::OPEN->value,
                    'description' => $dto->description,
                    'images' => $images,

                    'requester_id' => $dto->requesterId,
                    'type' => $dto->type,
                    'priority' => $dto->priority,
                    'category' => $dto->category,

                    'sla_response_due_at' => $this->businessHoursService->addBusinessMinutes(now(), $sla->response_time_minutes),
                    'sla_resolution_due_at' => $this->businessHoursService->addBusinessMinutes(now(), $sla->resolution_time_minutes),

                ]);



                $this->logHistory($ticket->id, TicketHistoryAction::CREATED, 'Ticket creado', $dto->requesterId);

            });
        } catch (\Exception $e) {
         
            if (count($images) > 0) {
                foreach ($images as $path) {
                    Storage::disk('public')->delete($path);
                }
            }
            throw new InternalErrorException("Error al crear el ticket: " . $e->getMessage());
        }


    }



    public function updateTicket(Ticket $ticket, StoreTicketDto $dto)
    {

       
        if ($dto->requesterId !== $ticket->requester_id) {
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

                if ($dto->images) {
                    if (is_array($ticket->images)) {
                        foreach ($ticket->images as $oldImage) {
                            Storage::disk('public')->delete($oldImage);
                        }
                    }

                    $images = [];
                    foreach ($dto->images as $image) {
                        $path = CompressImage::save($image, 'ticket_images');
                        $images[] = $path;
                    }
                    $ticket->images = $images;
                }

                $original = $ticket->getOriginal();

                $ticket->title = $dto->title;
               
                // $ticket->requester_id = $dto->requesterId;
                $ticket->description = $dto->description;
                $ticket->type = $dto->type;
                $ticket->impact = $dto->impact;
                $ticket->urgency = $dto->urgency;
                $ticket->priority = $dto->priority;
                $ticket->category = $dto->category;
                
                $ticket->save();



                $changes = $ticket->getChanges();
                if (count($changes) > 0) {
                    $changeDetails = [];
                    foreach ($changes as $field => $newValue) {
                        $oldValue = $original[$field] ?? null;

                        if ($oldValue === $newValue || $field === 'updated_at') {
                            continue;
                        }

                        if ($field === 'requester_id') {
                            $oldRequester = User::select('staff_id', 'firstname', 'lastname')->find($oldValue);
                            $newRequester = User::select('staff_id', 'firstname', 'lastname')->find($newValue);
                            $oldName = $oldRequester ? $oldRequester->full_name : 'N/A';
                            $newName = $newRequester ? $newRequester->full_name : 'N/A';
                            $changeDetails[] = "Solicitante cambiado de '{$oldName}' a '{$newName}'";
                            continue;
                        }

                        if ($field === 'category') {
                            if ($oldValue === null) {
                                $changeDetails[] = "{$this->fieldsLabels($field)} establecido a '" . TicketRequestType::label($newValue) . "'";
                            } elseif ($newValue === null) {
                                $changeDetails[] = "{$this->fieldsLabels($field)} eliminado (antes '" . TicketRequestType::label($oldValue) . "')";
                            }
                            continue;

                        }

                        if ($field === 'images') {
                            $changeDetails[] = "Imágenes actualizadas";
                            continue;
                        }

                        switch ($field) {

                            case 'impact':
                                $oldValue = TicketImpact::label($oldValue);
                                $newValue = TicketImpact::label($newValue);
                                break;

                            case 'urgency':
                                $oldValue = TicketUrgency::label($oldValue);
                                $newValue = TicketUrgency::label($newValue);
                                break;

                            case 'priority':
                                $oldValue = TicketPriority::label($oldValue);
                                $newValue = TicketPriority::label($newValue);
                                break;
                            case 'type':
                                $oldValue = TicketType::label($oldValue);
                                $newValue = TicketType::label($newValue);
                                break;
                            case 'category':
                                $oldValue = TicketCategory::label($oldValue);
                                $newValue = TicketCategory::label($newValue);
                                break;
                        }



                        $changeDetails[] = "{$this->fieldsLabels($field)} cambiado de '{$oldValue}' a '{$newValue}'";
                    }
                    $changeDescription = implode('; ', $changeDetails);

                    $this->logHistory($ticket->id, TicketHistoryAction::UPDATED, $changeDescription, $dto->requesterId);
                }

                if ($original['requester_id'] !== $dto->requesterId) {
                    $ticket->load('requester:staff_id,firstname,lastname');
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
            'category' => 'Categoría',
                'impact' => 'Impacto',
                'urgency' => 'Urgencia',
                'images' => 'Imágenes',
            default => $field,
        };
    }

    public function attachAssetToTicket(
        int $ticketId,
        int $assetId,
        TicketAssetAction $action,
        int $assignmentId
    ) {


        TicketAsset::create([
            'ticket_id' => $ticketId,
            'asset_id' => $assetId,
            'action' => $action->value,
            'asset_assignment_id' => $assignmentId,
            'performed_by' => auth()->user()->staff_id,

        ]);


    }

    function logHistory(int $ticketId, TicketHistoryAction $action, ?string $description = null, $performerId = null)
    {
        TicketHistory::create([
            'action' => $action->value,
            'ticket_id' => $ticketId,
            'performed_by' => $performerId ?? auth()->id(),
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

        if ($ticket->status !== TicketStatus::IN_PROGRESS->value) {
            throw new BadRequestException("No se puede asignar un ticket que no esté en estado 'En progreso'.");
        }

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
                    ? "Cambio de responsable de '{$oldResponsible->full_name}' a '{$responsible->full_name}'"
                    : "Responsable asignado '{$responsible->full_name}'";



                $this->logHistory($ticket->id, TicketHistoryAction::RESPONSIBLE_CHANGED, $description);

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
        $oldStatus = $ticket->status;

        if ($oldStatus === $newStatus) {
            throw new BadRequestException("El ticket ya tiene el estado seleccionado.");
        }

        if (!$this->canTransition($oldStatus, $newStatus)) {
            throw new BadRequestException("Transición de estado no permitida de '" . TicketStatus::label($oldStatus) . "' a '" . TicketStatus::label($newStatus) . "'.");
        }

        try {

            return DB::transaction(function () use ($ticket, $newStatus, $oldStatus) {
                $ticket->status = $newStatus;

                if ($newStatus === TicketStatus::IN_PROGRESS->value) {
                    if ($ticket->first_response_at === null) {
                        $ticket->first_response_at = now();
                    } else {
                        $pausedMinutes = Carbon::parse($ticket->sla_paused_at)->diffInMinutes(now());
                        $ticket->sla_paused_duration += $pausedMinutes;
                        $ticket->sla_paused_at = null;
                    }
                }

                if ($newStatus === TicketStatus::ON_HOLD->value) {
                    $ticket->sla_paused_at = now();
                }

                if ($newStatus === TicketStatus::RESOLVED->value) {

                    if ($ticket->responsible_id === null) {
                        throw new BadRequestException("No se puede resolver un ticket que no tiene un responsable asignado.");
                    }

                    $ticket->resolved_at = now();

                    $totalBusinessMinutes =
                        $this->businessHoursService->businessMinutesBetween(
                            $ticket->created_at,
                            $ticket->resolved_at
                        );

                    $realConsumedMinutes =
                        $totalBusinessMinutes - $ticket->sla_paused_duration;


                        $sla_resolution_minutes = SlaPolicy::where('priority', $ticket->priority)->value('resolution_time_minutes');
                    

                    if ($realConsumedMinutes > $sla_resolution_minutes) {
                        $ticket->sla_breached = true;
                    }
                }


                $ticket->save();

                $description = $newStatus === TicketStatus::CLOSED->value
                    ? "Cerrado el ticket"
                    : "Cambiado de estado de '" . TicketStatus::label($oldStatus) . "' a " . "'" . TicketStatus::label($newStatus) . "'";

                $this->logHistory($ticket->id, TicketHistoryAction::STATUS_CHANGED, $description);

                return [
                    'description' => $description,
                    'ticket' => $ticket,
                ];
            });
        } catch (\Exception $e) {
            throw new InternalErrorException("Error al cambiar el estado del ticket: " . $e->getMessage());
        }
    }



    private function canTransition(string $from, string $to): bool
    {
        $allowedTransitions = [

            TicketStatus::OPEN->value => [
                TicketStatus::IN_PROGRESS->value,
            ],

            TicketStatus::IN_PROGRESS->value => [
                TicketStatus::ON_HOLD->value,
                TicketStatus::RESOLVED->value,
            ],

            TicketStatus::ON_HOLD->value => [
                TicketStatus::IN_PROGRESS->value,
            ],

            TicketStatus::RESOLVED->value => [
                TicketStatus::CLOSED->value,
            ],

            TicketStatus::CLOSED->value => [
                // Nada, estado final
            ],
        ];

        return in_array($to, $allowedTransitions[$from] ?? []);
    }










}