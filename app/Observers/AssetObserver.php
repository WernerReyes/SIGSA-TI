<?php

namespace App\Observers;

use App\Models\Asset;
use App\Services\AccessoryOutOfStockAlertService;

class AssetObserver
{


    /**
     * Handle the AssetAssignment "updated" event.
     */
    /**
     * Handle the AssetAssignment "deleted" event.
     */
    public function deleted(Asset $asset): void
    {
        app(AccessoryOutOfStockAlertService::class)->check();
    }
}
