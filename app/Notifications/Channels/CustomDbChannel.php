<?php
namespace App\Notifications\Channels;

use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Notification;

class CustomDbChannel extends  DatabaseChannel {
    // public function send($notifiable, Notification $notification)
    // {
    //     return $notifiable->routeNotificationFor('database') == false
    //         ? null
    //         : $notifiable->notifications()->create([
    //             'id' => $notification->id,
    //             'type' => get_class($notification),
    //             'data' => $this->getData($notifiable, $notification),
    //             'read_at' => null,
    //             'entity' => $notification->entity_id
    //         ]);
    // }

    protected function buildPayload($notifiable, Notification $notification)
    {
        return [
            'id' => $notification->id,
            'type' => method_exists($notification, 'databaseType')
                ? $notification->databaseType($notifiable)
                : get_class($notification),
            'data' => $this->getData($notifiable, $notification),
            'read_at' => method_exists($notification, 'initialDatabaseReadAtValue')
                ? $notification->initialDatabaseReadAtValue($notifiable)
                : null,
                'entity_id' => $notification->entity_id
        ];
    }
}