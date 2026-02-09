<?php

namespace App\Notifications;

use App\Enums\Contract\ContractType;
use App\Enums\notification\NotificationEntity;
use App\Models\Contract;
use App\Models\ContractExpiration;
use App\Notifications\Channels\CustomDbChannel;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ContractExpirationNotification extends Notification
{
    use Queueable;

    private ContractExpiration $expiration;
    private Contract $contract;

    public int $entity_id;

    /**
     * Create a new notification instance.
     */
    public function __construct(ContractExpiration $expiration)
    {
        $this->expiration = $expiration;
        $this->contract = $expiration->contract->load('expiration', 'billing');
        $this->entity_id = $expiration->contract_id;
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

    public function toBroadcast(object $notifiable): BroadcastMessage
    {


        return new BroadcastMessage([
            'type' => NotificationEntity::CONTRACT->value,
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
        // $contract = $this->expiration->contract;
        $label = ContractType::label($contract->type);
        if ($this->expiration) {
            $days = $this->getRemainingDays($this->expiration->expiration_date);


            if ($days === 0) {
                return [
                    'message' => "{$label}: {$contract->name} vence hoy",
                    'short' => 'vence hoy',
                ];
            }
            if ($days === 1) {
                return [
                    'message' => "{$label}: {$contract->name} vence mañana",
                    'short' => 'vence mañana',
                ];
            }
            if ($days > 1) {
                return [
                    'message' => "{$label}: {$contract->name} vence en {$days} días",
                    'short' => 'vence en ' . $days . ' días',
                ];
            }

        }
        return [
            'message' => "{$label}: {$contract->name} se renovó automáticamente",
            'short' => 'se renovó automáticamente',
        ];

    }

    public function getRemainingDays($expirationDate): int
    {
        if (!$expirationDate) {
            return 0;
        }
        $date = $expirationDate instanceof Carbon ? $expirationDate : Carbon::parse($expirationDate);
        $now = Carbon::now();
        $date = $date->endOfDay();
        return $now->diffInDays($date, false);
    }
}
