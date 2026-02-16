<?php

use App\Http\Middleware\CheckCharge;
use App\Http\Middleware\CheckDepartment;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__ . '/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->validateCsrfTokens(except: [
            'login',
            // 'webhook/*',
        ]);

        $middleware->alias([
            'department' => CheckDepartment::class,
            'charge' => CheckCharge::class,
        ]);

        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (NotFoundHttpException $e) {
            $message = $e->getMessage();
            $previous = $e->getPrevious();

            if ($previous instanceof ModelNotFoundException) {
                $modelClass = $previous->getModel();
                $message = match ($modelClass) {
                    \App\Models\Ticket::class => 'El ticket solictado ya no existe',
                    \App\Models\Asset::class => 'El equipo solictado ya no existe',
                    \App\Models\AssetAssignment::class => 'La asignación de equipo solicitada ya no existe',
                    \App\Models\Service::class => 'La solicitud de servicio solicitada ya no existe',
                    \App\Models\Contract::class => 'El contrato solicitado ya no existe',
                    \App\Models\InfrastructureEvents::class => 'El evento de infraestructura solicitado ya no existe',
                    \App\Models\DevelopmentRequest::class => 'La solicitud de desarrollo solicitada ya no existe',
                    \App\Models\Notification::class => 'La notificación solicitada ya no existe',
                    default => 'El recurso solicitado ya no existe'
                };

                $message .= ", por favor recarga la página para actualizar los datos.";
            }

            return back()->with([
                'error' => $message,
                'success' => null,
                'timestamp' => now()->timestamp,
            ])->setStatusCode(303);

        });


    })->create();

