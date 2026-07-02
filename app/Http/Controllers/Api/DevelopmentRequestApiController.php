<?php

namespace App\Http\Controllers\Api;

use App\DTOs\DevelopmentRequest\StoreDevelopmentRequestDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\DevelopmentRequest\StoreDevelopmentRequest;
use App\Models\DevelopmentRequest;
use App\Services\DevelopmentRequestService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class DevelopmentRequestApiController extends Controller
{
    public function index(Request $request, DevelopmentRequestService $service)
    {
        $requestedById = $request->query('requested_by_id');

        try {
            return response()->json([
                'data' => $service->getSectionsByStatus($requestedById),
            ]);
        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(StoreDevelopmentRequest $request, DevelopmentRequestService $service)
    {
        $validated = $request->validated();
        $dto = StoreDevelopmentRequestDto::fromArray($validated);

        try {
            $developmentRequest = $service->store($dto);

            return response()->json([
                'message' => 'Solicitud de desarrollo creada exitosamente.',
                'data' => $developmentRequest,
            ], 201);
        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function progressHistory(int $id, DevelopmentRequestService $service)
    {
        try {
            DevelopmentRequest::findOrFail($id);

            return response()->json([
                'data' => $service->getProgressHistory($id),
            ]);
        } catch (ModelNotFoundException) {
            return response()->json([
                'error' => 'Solicitud de desarrollo no encontrada.',
            ], 404);
        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    private function errorResponse(Throwable $e)
    {
        return response()->json([
            'error' => $e->getMessage(),
        ], $this->statusCodeFor($e));
    }

    private function statusCodeFor(Throwable $e): int
    {
        if ($e instanceof ModelNotFoundException) {
            return 404;
        }

        if ($e instanceof BadRequestException) {
            return 400;
        }

        if ($e instanceof UnauthorizedException) {
            return 403;
        }

        if ($e instanceof HttpExceptionInterface) {
            return $e->getStatusCode();
        }

        return 500;
    }
}
