<?php

namespace App\Notifications;

use App\Models\ContractBilling;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractRenewalNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    private ContractBilling $billing;

    /**
     * Create a new notification instance.
     */
    public function __construct(ContractBilling $billing)
    {
        $this->billing = $billing;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        // ds('Generando notificación de renovación para contrato', $this->billing->contract_id);
        return new BroadcastMessage([
            'message' => 'Tu contrato está próximo a renovarse.',
            'contract_id' => $this->billing->contract_id,
            'next_billing_date' => $this->billing->next_billing_date,
        ]);
    }

    public function toArray($notifiable)
    {
        return [
            'contract_id' => $this->billing->contract_id,
            'message' => "El contrato {$this->billing->contract_id} está por renovarse",
        ];
    }
}
