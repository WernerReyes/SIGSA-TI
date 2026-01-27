<?php
namespace App\Services;

use App\DTOs\DevelopmentRequest\StoreDevelopmentRequestDto;
use App\Enums\DevelopmentRequest\DevelopmentRequestStatus;

use App\Models\DevelopmentRequest;
// use Illuminate\Container\Attributes\Storage;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
class DevelopmentRequestService
{

    public function getAll()
    {
        return DevelopmentRequest::with(['area', 'requestedBy:staff_id,firstname,lastname'])->get();
    }

    public function store(StoreDevelopmentRequestDto $dto)
    {
        $path = null;

        try {
            if ( $dto->requirement_file ){

                $path = Storage::disk('public')->putFile('delivery_records', $dto->requirement_file);
            }

            ds( $path, $dto->requirement_file );
            DevelopmentRequest::create([
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

        } catch (\Exception $e) {
            ds($e->getMessage());
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            throw new InternalErrorException('Error al crear la solicitud de desarrollo: ' . $e->getMessage());
        }
    }
}