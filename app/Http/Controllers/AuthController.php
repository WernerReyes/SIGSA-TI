<?php

namespace App\Http\Controllers;

use App\DTOs\Auth\LoginDto;
use App\Http\Requests\Auth\LoginRequest;

use App\Services\AuthService;
use Auth;
use Inertia\Inertia;


class AuthController extends Controller
{
    
    function renderLoginView()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return Inertia::render('auth/Login');
    }
    function login(LoginRequest $request, AuthService $authService)
    {
        $credentials = $request->validated();
        $dto = LoginDto::fromArray($credentials);

        return $authService->login($dto);
    }

    function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    }
}
