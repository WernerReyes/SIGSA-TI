<?php
namespace App\Services;

use App\DTOs\DevelopmentRequest\ApproveDevelopmentDto;
use App\DTOs\DevelopmentRequest\EstimateDevelopmentDto;
use App\DTOs\DevelopmentRequest\StoreDevelopmentProgressDto;
use App\DTOs\DevelopmentRequest\StoreDevelopmentRequestDto;
use App\Enums\DevelopmentRequest\DevelopmentApprovalLevel;
use App\Enums\DevelopmentRequest\DevelopmentApprovalStatus;
use App\Enums\DevelopmentRequest\DevelopmentRequestStatus;

use App\Enums\User\UserCharge;
use App\Models\DevelopmentApproval;
use App\Models\DevelopmentProgress;
use App\Models\DevelopmentRequest;
// use Illuminate\Container\Attributes\Storage;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
class DevelopmentRequestService
{

    public function getAll()
    {
        return DevelopmentRequest::with([
            'area',
            'requestedBy:staff_id,firstname,lastname',
            'technicalApproval.approvedBy:staff_id,firstname,lastname',
            'strategicApproval.approvedBy:staff_id,firstname,lastname
        '
        ])->get();
    }

    public function getSectionsByStatus()
    {
        $requests = DevelopmentRequest::with([
            'area',
            'requestedBy:staff_id,firstname,lastname',
            'technicalApproval.approvedBy:staff_id,firstname,lastname',
            'strategicApproval.approvedBy:staff_id,firstname,lastname',
            'latestProgress',
            'developers:staff_id,firstname,lastname',
        ])->orderBy('position', 'ASC')->get();

        return $requests->groupBy('status');
    }


    public function getProgressHistory(int $depReqId)
    {
        return DevelopmentProgress::with([
            'performedBy:staff_id,firstname,lastname'
        ])->where('development_request_id', $depReqId)
            ->orderBy('created_at', 'ASC')
            ->get();
    }

    public function store(StoreDevelopmentRequestDto $dto)
    {
        $path = null;

        try {
            if ($dto->requirement_file) {
                $path = Storage::disk('public')->putFile('delivery_records', $dto->requirement_file);
            }

            $maxPosition = DevelopmentRequest::where('status', DevelopmentRequestStatus::REGISTERED->value)->max('position')
                ?? 0;

            $dev = DevelopmentRequest::create([
                'title' => $dto->title,
                'priority' => $dto->priority,
                'status' => DevelopmentRequestStatus::REGISTERED->value,
                'position' => $maxPosition + 1,
                'description' => $dto->description,
                'impact' => $dto->impact,
                'estimated_hours' => $dto->estimated_hours,
                'estimated_end_date' => $dto->estimated_end_date,
                'area_id' => $dto->area_id,
                'requested_by_id' => $dto->requested_by_id,
                'requirement_path' => $path,
            ]);
            return $dev->load(['area', 'requestedBy:staff_id,firstname,lastname']);
        } catch (\Exception $e) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            throw new InternalErrorException('Error al crear la solicitud de desarrollo: ' . $e->getMessage());
        }
    }

    public function update(DevelopmentRequest $developmentRequest, StoreDevelopmentRequestDto $dto)
    {

        if ($developmentRequest->requested_by_id !== $dto->requested_by_id) {
            throw new UnauthorizedException('No tienes permiso para actualizar esta solicitud de desarrollo.');
        }

        $path = $developmentRequest->requirement_path;
        try {
            if ($dto->requirement_file) {
                if ($path) {
                    Storage::disk('public')->delete($path);
                }
                $path = Storage::disk('public')->putFile('delivery_records', $dto->requirement_file);
            }

            $developmentRequest->update([
                'title' => $dto->title,
                'priority' => $dto->priority,
                'description' => $dto->description,
                'impact' => $dto->impact,
                'estimated_hours' => $dto->estimated_hours,
                'estimated_end_date' => $dto->estimated_end_date,
                'area_id' => $dto->area_id,
                'requirement_path' => $path,
            ]);

            if ($developmentRequest->wasChanged('area_id')) {
                $developmentRequest->load('area');
            }

            return $developmentRequest;
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al actualizar la solicitud de desarrollo: ' . $e->getMessage());
        }
    }

    public function swapPositions(array $devsIdsInOrder, string $status)
    {
        try {
            foreach ($devsIdsInOrder as $index => $devId) {
                DevelopmentRequest::where('id', $devId)->where('status', $status)->update(['position' => $index + 1]);
            }
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al actualizar las posiciones de las solicitudes de desarrollo: ' . $e->getMessage());
        }
    }
    public function updateStatus(DevelopmentRequest $developmentRequest, string $newStatus, array $devsIdsInOrder = [])
    {
        $fromStatus = $developmentRequest->status;
        if ($fromStatus === $newStatus) {
            throw new BadRequestException('El nuevo estado es igual al estado actual.');
        }

        if ($newStatus === DevelopmentRequestStatus::COMPLETED->value && $developmentRequest->latestProgress?->percentage < 100) {
            throw new BadRequestException('No se puede mover a Producción si el progreso no es 100%.');
        }


        if (
            in_array($fromStatus, [
                DevelopmentRequestStatus::REJECTED->value,
                DevelopmentRequestStatus::COMPLETED->value,
            ])
        ) {
            throw new BadRequestException('No se puede cambiar el estado de una solicitud que ya está en estado terminal.');
        }

        $fromIndex = array_search($fromStatus, array_map(fn($status) => $status->value, DevelopmentRequestStatus::cases()));
        $toIndex = array_search($newStatus, array_map(fn($status) => $status->value, DevelopmentRequestStatus::cases()));

        if ($toIndex !== $fromIndex + 1) {
            throw new BadRequestException('El estado solo puede avanzar de uno en uno sin retroceder ni saltar.');
        }


        if ($fromStatus === DevelopmentRequestStatus::IN_ANALYSIS->value) {
            $approvals = $developmentRequest->approvals;
            $allApproved = $approvals->every(fn($approval) => $approval->status === DevelopmentApprovalStatus::APPROVED->value);
            if (!$allApproved) {
                throw new BadRequestException('No se puede avanzar al siguiente estado porque no todas las aprobaciones están aprobadas.');
            }
        }

        try {
            return DB::transaction(function () use ($developmentRequest, $newStatus, $devsIdsInOrder) {
                $developmentRequest->status = $newStatus;
                $developmentRequest->save();

                $progress = null;

                $completed = null;

                if ($newStatus === DevelopmentRequestStatus::IN_DEVELOPMENT->value) {
                    $progress = DevelopmentProgress::create([
                        'development_request_id' => $developmentRequest->id,
                        'percentage' => 0,
                        'notes' => 'Inicio de desarrollo',
                        'performed_by' => auth()->user()->staff_id,
                    ]);
                }

                if ($newStatus === DevelopmentRequestStatus::COMPLETED->value) {
                    $totalHours = 0;

                    $startProgress = $developmentRequest->firstProgress;
                    $endProgress = $developmentRequest->latestProgress;

                    if ($startProgress && $endProgress) {
                        $startDate = Carbon::parse($startProgress->created_at);
                        $endDate = Carbon::parse($endProgress->created_at);
                        $totalHours = round($startDate->diffInHours($endDate), 3);
                     
                    }

                    $completed = [
                        'completed_at' => Carbon::now()->toDateString(),
                        'actual_hours' => $totalHours,
                    ];


                    // n: Update the migration to add actual_hours and completed_at fields
                    $developmentRequest->update([
                        'completed_at' => $completed['completed_at'],
                        'actual_hours' => $completed['actual_hours'],
                    ]);
                }


                if (!empty($devsIdsInOrder)) {
                    $this->swapPositions($devsIdsInOrder, $newStatus);
                }

                return [
                    'progress' => $progress,
                    'completed' => $completed,
                ];

            });
        } catch (\Exception $e) {
           
            throw new InternalErrorException('Error al actualizar el estado de la solicitud de desarrollo: ' . $e->getMessage());
        }

    }


    public function delete(DevelopmentRequest $developmentRequest)
    {
        if ($developmentRequest->requested_by_id !== auth()->user()->staff_id) {
            throw new UnauthorizedException('No tienes permiso para eliminar esta solicitud de desarrollo.');
        }

        if (
            in_array($developmentRequest->status, [
                DevelopmentRequestStatus::APPROVED->value,
                DevelopmentRequestStatus::IN_DEVELOPMENT->value,
                DevelopmentRequestStatus::IN_TESTING->value,
                DevelopmentRequestStatus::COMPLETED->value,
            ])
        ) {
            throw new BadRequestException('No se puede eliminar una solicitud que está en desarrollo, en pruebas o completada.');
        }

        try {
            DB::transaction(function () use ($developmentRequest) {

                $developmentRequest->approvals()->delete();


                $developmentRequest->delete();
            });

            if ($developmentRequest->requirement_path) {
                Storage::disk('public')->delete($developmentRequest->requirement_path);
            }
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al eliminar la solicitud de desarrollo: ' . $e->getMessage());
        }
    }

    public function estimateDevelopment(DevelopmentRequest $developmentRequest, EstimateDevelopmentDto $dto)
    {
        if ($developmentRequest->status !== DevelopmentRequestStatus::IN_ANALYSIS->value) {
            throw new BadRequestException('Solo se pueden estimar solicitudes en estado "En Análisis".');
        }

        if ($developmentRequest->technicalApproval || $developmentRequest->strategicApproval) {
            throw new BadRequestException('No se puede estimar una solicitud que ya tiene aprobaciones.');
        }

        try {
            $developmentRequest->update([
                'estimated_hours' => $dto->estimated_hours,
                'estimated_end_date' => Carbon::parse($dto->estimated_end_date),
            ]);
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al estimar la solicitud de desarrollo: ' . $e->getMessage());
        }
    }


    public function approveTechnicalDevelopment(DevelopmentRequest $developmentRequest, ApproveDevelopmentDto $dto)
    {

        if (UserCharge::TI_MANAGER->value !== $dto->approvedBy->id_cargo) {
            throw new UnauthorizedException('No tienes permiso para aprobar esta solicitud.');
        }

        if ($developmentRequest->estimated_hours === null || $developmentRequest->estimated_end_date === null) {
            throw new BadRequestException('La solicitud de desarrollo debe tener una estimación antes de ser aprobada técnicamente.');
        }

        if ($developmentRequest->technicalApproval) {
            throw new BadRequestException('Ya existe una aprobación técnica para esta solicitud de desarrollo.');
        }

        try {
            DB::transaction(function () use ($developmentRequest, $dto) {
                DevelopmentApproval::create([
                    'development_request_id' => $developmentRequest->id,
                    'approved_by_id' => $dto->approvedBy->staff_id,
                    'level' => DevelopmentApprovalLevel::TECHNICAL->value,
                    'status' => $dto->status,
                    'comments' => $dto->comments,
                ]);

            

                if ($dto->status === DevelopmentApprovalStatus::APPROVED->value && $developmentRequest->strategicApproval && $developmentRequest->strategicApproval->status === DevelopmentApprovalStatus::APPROVED->value) {
                    $developmentRequest->update(['status' => DevelopmentRequestStatus::APPROVED->value]);
                }

                if ($dto->status === DevelopmentApprovalStatus::REJECTED->value) {

                    $developmentRequest->update(['status' => DevelopmentRequestStatus::REJECTED->value]);
                }
            });


        } catch (\Exception $e) {

            throw new InternalErrorException('Error al crear la aprobación de la solicitud de desarrollo: ' . $e->getMessage());
        }

    }


    public function approveStrategicDevelopment(DevelopmentRequest $developmentRequest, ApproveDevelopmentDto $dto)
    {
        if (UserCharge::TI_ASSISTANT_MANAGER->value !== $dto->approvedBy->id_cargo) {
            throw new UnauthorizedException('No tienes permiso para aprobar esta solicitud.');
        }

        if ($developmentRequest->estimated_hours === null || $developmentRequest->estimated_end_date === null) {
            throw new BadRequestException('La solicitud de desarrollo debe tener una estimación antes de ser aprobada técnicamente.');
        }

        if ($developmentRequest->strategicApproval) {
            throw new BadRequestException('Ya existe una aprobación estratégica para esta solicitud de desarrollo.');
        }

        try {
            DevelopmentApproval::create([
                'development_request_id' => $developmentRequest->id,
                'approved_by_id' => $dto->approvedBy->staff_id,
                'level' => DevelopmentApprovalLevel::STRATEGIC->value,
                'status' => $dto->status,
                'comments' => $dto->comments,
            ]);

            if ($dto->status === DevelopmentApprovalStatus::APPROVED->value && $developmentRequest->technicalApproval && $developmentRequest->technicalApproval->status === DevelopmentApprovalStatus::APPROVED->value) {
                $developmentRequest->update(['status' => DevelopmentRequestStatus::APPROVED->value]);
            }

            if ($dto->status === DevelopmentApprovalStatus::REJECTED->value) {
                $developmentRequest->update(['status' => DevelopmentRequestStatus::REJECTED->value]);
            }
        } catch (\Exception $e) {
      
            throw new InternalErrorException('Error al crear la aprobación de la solicitud de desarrollo: ' . $e->getMessage());
        }

    }

    public function registerProgress(DevelopmentRequest $developmentRequest, StoreDevelopmentProgressDto $dto)
    {
        if (!in_array($developmentRequest->status, [DevelopmentRequestStatus::IN_DEVELOPMENT->value, DevelopmentRequestStatus::IN_TESTING->value])) {
            throw new BadRequestException('Solo se puede registrar progreso en solicitudes que estén en desarrollo o en pruebas.');
        }

        try {
            $progress = DevelopmentProgress::create([
                'development_request_id' => $developmentRequest->id,
                'percentage' => $dto->percentage,
                'notes' => $dto->notes ?? null,
                'performed_by' => $dto->performedBy,
            ]);

            return $progress;
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al registrar el progreso: ' . $e->getMessage());
        }
    }

    public function assignDevelopers(DevelopmentRequest $developmentRequest, array $developerIds)
    {
        if ($developmentRequest->status !== DevelopmentRequestStatus::IN_DEVELOPMENT->value) {
            throw new BadRequestException('Solo se pueden asignar desarrolladores en solicitudes que estén en desarrollo.');
        }

        try {
            $developmentRequest->developers()->sync($developerIds);

            // return $developmentRequest->fresh('developers');
        } catch (\Exception $e) {
          
            throw new InternalErrorException('Error al asignar desarrolladores: ' . $e->getMessage());
        }
    }


    public function comeBackToAnalysis(DevelopmentRequest $developmentRequest, array $devsReqIdsInOrder = [])
    {
        if ($developmentRequest->status !== DevelopmentRequestStatus::REJECTED->value) {
            throw new BadRequestException('Solo se pueden volver a analizar solicitudes que estén rechazadas.');
        }

        try {
            $maxPosition = DevelopmentRequest::where('status', DevelopmentRequestStatus::IN_ANALYSIS->value)->max('position')
                ?? 0;

            $developmentRequest->update([
                'status' => DevelopmentRequestStatus::IN_ANALYSIS->value,
                'position' => $maxPosition + 1,
            ]);
            $developmentRequest->approvals()->delete();

            if (!empty($devsReqIdsInOrder)) {
                $this->swapPositions($devsReqIdsInOrder, DevelopmentRequestStatus::IN_ANALYSIS->value);
            }

        } catch (\Exception $e) {
           
            throw new InternalErrorException('Error al volver a analizar la solicitud de desarrollo: ' . $e->getMessage());
        }
    }


}