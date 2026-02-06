<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    ds('Autorizando canal para usuario', $user, $id);
    return (int) $user->staff_id === (int) $id;
});
