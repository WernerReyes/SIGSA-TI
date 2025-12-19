<?php

use App\Models\User;
class UserService {
    function getTechnicians() {
        return User::whereIn('cargo_id', [21,31,32])->get();
    }
}