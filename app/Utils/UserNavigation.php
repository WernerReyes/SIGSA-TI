<?php
namespace App\Utils;

use App\Enums\Department\Allowed;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class UserNavigation
{
    public static function redirectBasedOnDepartment(User $user, $intented = false): RedirectResponse
    {
        $isFromRRHH = in_array($user->dept_id, [Allowed::RRHH->value]);
        if ($intented) {
            return redirect()->intended($isFromRRHH ? '/dashboard-rrhh' : '/dashboard');
        }
        return redirect()->route($isFromRRHH ? 'dashboard.rrhh' : 'dashboard');
    }
}