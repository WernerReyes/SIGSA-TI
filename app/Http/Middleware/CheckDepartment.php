<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDepartment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$departments): Response
    {

        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!in_array($user->dept_id, $departments)) {
            return redirect()->route('dashboard')->withErrors(['error' => 'No tienes permiso para acceder a esta sección.']);
        }

        return $next($request);
    }
}
