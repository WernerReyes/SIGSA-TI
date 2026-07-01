<?php

namespace App\Http\Controllers\Api;

use App\DTOs\DevelopmentRequest\StoreDevelopmentRequestDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\DevelopmentRequest\StoreDevelopmentRequest;
use App\Services\DevelopmentRequestService;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class DevelopmentRequestApiController extends Controller
{
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

    private function errorResponse(Throwable $e)
    {
        return response()->json([
            'error' => $e->getMessage(),
        ], $this->statusCodeFor($e));
    }

    private function statusCodeFor(Throwable $e): int
    {
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
