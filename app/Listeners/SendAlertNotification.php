<?php

namespace App\Listeners;

use App\Enums\Alert\AlertType;
use App\Events\AlertTriggered;
use App\Mail\AccessoryOutOfStockMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;
class SendAlertNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AlertTriggered $event): void
    {
        ds("Sending alert notification for alert ID", $event->alert->type === AlertType::ACCESSORY_OUT_OF_STOCK->value);
        if ($event->alert->type === AlertType::ACCESSORY_OUT_OF_STOCK->value) {
            // Send notification to purchases department
            $email = config('mail.purchases_email');
            ds($email);
            Mail::to($email)->send(new AccessoryOutOfStockMail());
        }
    }
}
