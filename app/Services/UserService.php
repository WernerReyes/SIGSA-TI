<?php
namespace App\Services;

use App\Models\User;


class UserService {

    function getTIDepartmentUsers() {
        return User::where('dept_id', 11)->get();
    }

    function getTechnicians() {
        return User::whereIn('dept_id', [9,10])->get();
    }

    function getAllUsers() {
        return User::select('staff_id', 'firstname', 'lastname')->get();
    }
}