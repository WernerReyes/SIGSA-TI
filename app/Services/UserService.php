<?php
namespace App\Services;

use App\Models\User;
use Cache;


class UserService
{

    function getTIDepartmentUsers()
    {
        $ti = Cache::remember('ti_department_users', 60 * 60, function () {
            return User::active()->select('staff_id', 'firstname', 'lastname')->where('dept_id', 11)->get();
        });
        return $ti;
    }


    function getAllBasicInfo()
    {
        $users = Cache::remember('users_basic_info', 60 * 60, function () {
            return User::active()->select('staff_id', 'firstname', 'lastname', 'email')->get();
        });
        ds( 'Usuarios para sugerencias (servicio):', $users->find('email', 'werner.reyes@cechriza.com') );
        return $users;
    }




}