<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function renderBasicInfo(Request $request, UserService $userService)
    {
        $users = $userService->getAllBasicInfo();
        $component = $request->input('component', 'Users/BasicInfo');

        return Inertia::render($component, [
            'users' => $users,
        ]);
    }

}
