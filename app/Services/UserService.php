<?php
namespace App\Services;

use App\Models\User;


class UserService {

    function getTIDepartmentUsers() {
        return User::active()->where('dept_id', 11)->get();
    }

    function getTechnicians() {
        return User::active()->whereIn('dept_id', [9,10])->get();
    }

    function getAllUsers() {
        return User::active()->select('staff_id', 'firstname', 'lastname')->get();
    }
}