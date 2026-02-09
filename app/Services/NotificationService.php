<?php

use App\Models\Notification;
class NotificationService
{
    public function markAsRead(Notification $notification)
    {
        $notification->update(['read_at' => now()]);
    }
}