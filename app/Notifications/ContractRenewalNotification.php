<?php

namespace App\Notifications;

use App\Enums\Contract\ContractPeriod;
use App\Enums\Contract\ContractType;
use App\Enums\notification\NotificationEntity;
use App\Models\Contract;
use App\Models\ContractBilling;
use App\Notifications\Channels\CustomDbChannel;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ContractRenewalNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

   
    private Contract $contract;

    public int $entity_id;

    /**
     * Create a new notification instance.
     */
    public function __construct(ContractBilling $billing)
    {
    
        $this->contract = $billing->contract->load('expiration', 'billing');
        $this->entity_id = $billing->contract_id;
    }

    public function databaseType(object $notifiable): string
    {
        return NotificationEntity::CONTRACT->value;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [CustomDbChannel::class, 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {

    ds([
            // 'type' => NotificationEntity::CONTRACT->value,
            ...$this->getMessage($this->contract),
            'contract' => $this->contract,
        ]);

        return new BroadcastMessage([
            // 'type' => NotificationEntity::CONTRACT->value,
            ...$this->getMessage($this->contract),
            'contract' => $this->contract,
        ]);
    }

    public function toArray($notifiable)
    {

        return [
            'message' => $this->getMessage($this->contract)['short'],
             
        ];
    }


    private function getMessage(Contract $contract)
    {
        // $contract = $this->billing->contract;
        $label = ContractType::label($contract->type);
    
            return [
                'message' => "{$label}: {$contract->name} se renovó automáticamente",
                'short' => 'se renovo automaticamente',
            ];
        
       
    }


}
