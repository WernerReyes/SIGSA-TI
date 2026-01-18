<?php
namespace App\Services;

use App\Enums\Alert\AlertStatus;
use App\Enums\Alert\AlertType;
use App\Enums\Alert\EntityType;
use App\Enums\Asset\AssetStatus;
use App\Models\Asset;

class AccessoryOutOfStockAlertService extends BaseAlertService
{
    protected AlertType $alertType = AlertType::ACCESSORY_OUT_OF_STOCK;
    protected EntityType $entityType = EntityType::ASSET;

    protected array $messages = [
        AlertStatus::ACTIVE->value => 'No hay accesorios disponibles en el inventario.',
        AlertStatus::RESOLVED->value => 'Hay accesorios disponibles en el inventario.',
    ];
    protected ?int $cooldownHours = 24;

    protected function conditionMet(): bool
    {
        ds(Asset::where('status', AssetStatus::AVAILABLE)
            ->whereHas('type', fn($q) => $q->where('name', 'Accesorio'))->get());
        return !Asset::where('status', AssetStatus::AVAILABLE)
            ->whereHas('type', fn($q) => $q->where('name', 'Accesorio'))
            ->exists();
    }
}