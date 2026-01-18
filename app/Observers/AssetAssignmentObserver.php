<?php

namespace App\Observers;


use App\Models\AssetAssignment;
use App\Services\AccessoryOutOfStockAlertService;

class AssetAssignmentObserver
{
    

    public function created(AssetAssignment $assignment)
    {
        app(AccessoryOutOfStockAlertService::class)->check();

    }

    /**
     * Handle the AssetAssignment "updated" event.
     */
    public function updated(AssetAssignment $assetAssignment): void
    {
        ds('AssetAssignment updated observer triggered');
        app(AccessoryOutOfStockAlertService::class)->check();
    }

    /**
     * Handle the AssetAssignment "deleted" event.
     */
    public function deleted(AssetAssignment $assetAssignment): void
    {
        app(AccessoryOutOfStockAlertService::class)->check();
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
