<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CheckCharge
{
    
    public function handle(Request $request, Closure $next, ...$charges): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!in_array($user->id_cargo, $charges)) {
            Inertia::flash('error', 'No tienes permiso para acceder a esta sección.');
            return redirect()->route('dashboard');
        }
        
        return $next($request);
    }
}
