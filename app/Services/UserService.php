<?php
namespace App\Services;

use App\Models\User;
use Cache;
use App\Enums\User\UserDept;


class UserService
{

    function getTIDepartmentUsers($active = true)
    {
        $ti = Cache::remember('ti_department_users', 60 * 60, function () use ($active) {
            // return User::active()->select('staff_id', 'firstname', 'lastname')->where('dept_id', 11)->get();
            return User::active($active)->select('staff_id', 'firstname', 'lastname')->whereIn('dept_id', [UserDept::TI->value, UserDept::RRHH->value])->get();
        });
        return $ti;
    }


    function getAllBasicInfo()
    {
        $users = Cache::remember('users_basic_info', 60 * 60, function () {
            // return User::active()->select('staff_id', 'firstname', 'lastname', 'email')->get();
            return User::select('staff_id', 'firstname', 'lastname', 'email')->get();
        });
        return $users;
    }




}