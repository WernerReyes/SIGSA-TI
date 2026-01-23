<?php
namespace App\Services;

use App\Enums\Alert\AlertStatus;
use App\Enums\Alert\AlertType;
use App\Enums\Alert\EntityType;
use App\Events\AlertTriggered;
use App\Models\Alert;
use Inertia\Inertia;
abstract class BaseAlertService
{
    protected AlertType $alertType;

    protected array $messages;
    protected EntityType $entityType;

    protected ?int $entityId = null;

    // Tiempo mínimo entre notificaciones
    protected ?int $cooldownHours = null;

    protected bool $once = false;

    abstract protected function conditionMet(): bool;



    public function check(): void
    {
        $alert = Alert::firstOrCreate(
            [
                'type' => $this->alertType,
                'entity' => $this->entityType,
                'entity_id' => $this->entityId,
            ],
            [
                'status' => AlertStatus::RESOLVED->value,
            ]
        );

        if ($this->conditionMet()) {
            $this->activate($alert);
        } else {
            $this->resolve($alert);
        }
    }

    protected function activate(Alert $alert, bool $force = false): void
    {

        if ($alert->status === AlertStatus::ACTIVE->value && !$force) {
            return;
        }

        if (!$this->canNotify($alert) && !$force) {
            return;
        }

        $alert->update([
            'status' => AlertStatus::ACTIVE->value,
            // 'last_notified_at' => now(),
            'message' => $this->messages[AlertStatus::ACTIVE->value] ?? '',
        ]);

        Inertia::flash('alert_triggered', true);

        
        event(new AlertTriggered($alert));
    }

    protected function resolve(Alert $alert): void
    {
        if ($alert->status === AlertStatus::RESOLVED->value) {
            return;
        }

        $alert->update([
            'status' => AlertStatus::RESOLVED->value,
            'message' => $this->messages[AlertStatus::RESOLVED->value] ?? '',
        ]);

        Inertia::flash('alert_triggered', true);
    }

    protected function canNotify(Alert $alert): bool
    {
        if ($this->once) {
            return !$alert->last_notified_at;
        }

        if (!$alert->last_notified_at || !$this->cooldownHours) {
            return true;
        }


        return $alert->last_notified_at
            ->diffInHours(now()) >= $this->cooldownHours;
    }
}