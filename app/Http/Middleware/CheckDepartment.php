<?php

namespace App\Http\Middleware;

use App\Utils\UserNavigation;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
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
            Inertia::flash('error', 'No tienes permiso para acceder a esta sección.');
            return UserNavigation::redirectBasedOnDepartment($user);
        }

        return $next($request);
    }
}
