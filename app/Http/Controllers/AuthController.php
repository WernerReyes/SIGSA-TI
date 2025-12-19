<?php

namespace App\Http\Controllers;

use App\DTOs\Auth\LoginDto;
use App\Http\Requests\Auth\LoginRequest;

use App\Services\AuthService;
use Auth;


class AuthController extends Controller
{
    function login(LoginRequest $request, AuthService $authService)
    {

        $credentials = $request->validated();
        $dto = LoginDto::fromArray($credentials);

        return $authService->login($dto);
    }

    function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
