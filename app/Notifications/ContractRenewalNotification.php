<?php

namespace App\Notifications;

use App\Enums\Contract\BillingFrequency;
use App\Enums\Contract\ContractPeriod;
use App\Enums\notification\NotificationEntity;
use App\Models\Contract;
use App\Models\ContractBilling;
use App\Notifications\Channels\CustomDbChannel;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ContractRenewalNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    private ContractBilling $billing;
    private Contract $contract;

    public int $entity_id;

    /**
     * Create a new notification instance.
     */
    public function __construct(ContractBilling $billing)
    {
        $this->billing = $billing;
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

       
        return new BroadcastMessage([
            'message' => $this->getMessage($this->contract),
                'contract' => $this->contract,
        ]);
    }

    public function toArray($notifiable)
    {
       
        return [
            'message' => $this->getMessage($this->contract),
        ];
    }


    private function getMessage(Contract $contract) {
        // $contract = $this->billing->contract;
        if ($contract->period === ContractPeriod::RECURRING->value) {
            if ($this->billing->expiration) {
                $days = $this->getRemainingDays($this->billing->expiration, 0);
                if ($days === 0) {
                    return 'vence hoy';
                }
                if ($days === 1) {
                    return 'vence mañana';
                }
                if ($days > 1) {
                    return 'vence en ' . $days . ' días';
                }
                return 'venció hace ' . abs($days) . ' días';
            }
            return 'se renovo automaticamente';
        }
        return 'revisar vigencia del contrato';
    }

    public function getRemainingDays($expirationDate, $daysBefore): int
    {
        if (!$expirationDate) {
            return 0;
        }
        $date = $expirationDate instanceof Carbon ? $expirationDate : Carbon::parse($expirationDate);
        $now = Carbon::now();
        return $now->diffInDays($date, false) - (int) $daysBefore;
    }
    
}
