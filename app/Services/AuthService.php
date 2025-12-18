<?php

namespace App\Services;

use App\DTOs\Auth\LoginDto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class AuthService
{
    function login(LoginDto $dto)
    {
        $user = User::where('email', $dto->username)
            ->orWhere('username', $dto->username)->
            orWhere('dni', $dto->username)
            ->first();
        if (!$user || !password_verify($dto->password, $user->password)) {
            return back()->withErrors([
                'username' => 'Las credenciales proporcionadas no son correctas.',
            ]);
        }

        Auth::login($user);

        return redirect()->intended('/dashboard');
    }
}