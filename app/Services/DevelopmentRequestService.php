<?php
namespace App\Services;

use App\DTOs\DevelopmentRequest\EstimateDevelopmentDto;
use App\DTOs\DevelopmentRequest\StoreDevelopmentRequestDto;
use App\Enums\DevelopmentRequest\DevelopmentApprovalStatus;
use App\Enums\DevelopmentRequest\DevelopmentRequestStatus;

use App\Models\DevelopmentRequest;
// use Illuminate\Container\Attributes\Storage;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
class DevelopmentRequestService
{

    public function getAll()
    {
        return DevelopmentRequest::with(['area', 'requestedBy:staff_id,firstname,lastname', 'approvals.approvedBy:staff_id,firstname,lastname'])->get();
    }

    public function store(StoreDevelopmentRequestDto $dto)
    {
        $path = null;

        try {
            if ($dto->requirement_file) {
                $path = Storage::disk('public')->putFile('delivery_records', $dto->requirement_file);
            }
            $dev = DevelopmentRequest::create([
                'title' => $dto->title,
                'priority' => $dto->priority,
                'status' => DevelopmentRequestStatus::REGISTERED->value,
                'description' => $dto->description,
                'impact' => $dto->impact,
                'estimated_hours' => $dto->estimated_hours,
                'estimated_end_date' => $dto->estimated_end_date,
                'area_id' => $dto->area_id,
                'requested_by_id' => $dto->requested_by_id,
                'requirement_path' => $path,
            ]);
            return $dev->load(['area']);
        } catch (\Exception $e) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            throw new InternalErrorException('Error al crear la solicitud de desarrollo: ' . $e->getMessage());
        }
    }

    public function update(DevelopmentRequest $developmentRequest, StoreDevelopmentRequestDto $dto)
    {
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

    public function updateStatus(DevelopmentRequest $developmentRequest, string $newStatus)
    {
        $fromStatus = $developmentRequest->status;
        if ($fromStatus === $newStatus) {
            return;
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
            $developmentRequest->status = $newStatus;
            $developmentRequest->save();
        } catch (\Exception $e) {
            throw new InternalErrorException('Error al actualizar el estado de la solicitud de desarrollo: ' . $e->getMessage());
        }

    }


    public function estimateDevelopment(DevelopmentRequest $developmentRequest, EstimateDevelopmentDto $dto)
    {
        try {
            $developmentRequest->update([
                'estimated_hours' => $dto->estimated_hours,
                'estimated_end_date' => $dto->estimated_end_date,
                'people_amount' => $dto->people_amount,
            ]);

        } catch (\Exception $e) {
            throw new InternalErrorException('Error al estimar la solicitud de desarrollo: ' . $e->getMessage());
        }
    }



}