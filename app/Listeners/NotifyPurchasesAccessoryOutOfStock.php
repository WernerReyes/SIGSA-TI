<?php

namespace App\Listeners;

use App\Events\AccessoryOutOfStock;
use App\Mail\AccessoryOutOfStockMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class NotifyPurchasesAccessoryOutOfStock implements ShouldQueue
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
    public function handle(AccessoryOutOfStock $event): void
    {
        $email = config('mail.purchases_email');
        Mail::to($email)->send(new AccessoryOutOfStockMail());
    }
}
