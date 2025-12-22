<?php
namespace App\Services;

use App\Models\User;


class UserService {
    function getTechnicians() {
        return User::whereIn('dept_id', [9,10])->get();
    }
}