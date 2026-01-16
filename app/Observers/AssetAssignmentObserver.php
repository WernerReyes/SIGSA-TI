<?php

namespace App\Observers;

use App\Enums\Asset\AssetStatus;
use App\Events\AccessoryOutOfStock;
use App\Models\Asset;
use App\Models\AssetAssignment;

class AssetAssignmentObserver
{
    /**
     * Handle the AssetAssignment "created" event.
     */

    private function existsAvailableAccessories(): bool
    {
        return Asset::where('status', AssetStatus::AVAILABLE)
            ->whereHas('type', function ($query) {
                $query->where('name', 'Accesorio');
            })
            ->exists();
    }

    public function created(AssetAssignment $assignment)
    {
        if (!$this->existsAvailableAccessories()) {
            event(new AccessoryOutOfStock());
        }

    }

    /**
     * Handle the AssetAssignment "updated" event.
     */
    public function updated(AssetAssignment $assetAssignment): void
    {
        //
        if (!$this->existsAvailableAccessories()) {
            event(new AccessoryOutOfStock());
        }
    }

    /**
     * Handle the AssetAssignment "deleted" event.
     */
    public function deleted(AssetAssignment $assetAssignment): void
    {
        //
        if (!$this->existsAvailableAccessories()) {
            event(new AccessoryOutOfStock());
        }
    }

    /**
     * Handle the AssetAssignment "restored" event.
     */
    public function restored(AssetAssignment $assetAssignment): void
    {
        //
    }

    /**
     * Handle the AssetAssignment "force deleted" event.
     */
    public function forceDeleted(AssetAssignment $assetAssignment): void
    {
        //
    }
}
