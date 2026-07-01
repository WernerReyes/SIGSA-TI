<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccessApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredKey = config('services.access_api.key');
        $providedKey = $request->header('X-API-Key') ?? $request->bearerToken();

        if (!$configuredKey) {
            return response()->json([
                'error' => 'ACCESS_API no esta configurado en el servidor.',
            ], 500);
        }

        if (!$providedKey || !hash_equals((string) $configuredKey, (string) $providedKey)) {
            return response()->json([
                'error' => 'API key invalida o no enviada.',
            ], 401);
        }

        return $next($request);
    }
}
