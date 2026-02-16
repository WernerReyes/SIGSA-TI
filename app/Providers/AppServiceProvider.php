<?php

namespace App\Providers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Observers\AssetAssignmentObserver;
use App\Observers\AssetObserver;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        AssetAssignment::observe(AssetAssignmentObserver::class);
        Asset::observe(AssetObserver::class);
        Carbon::setLocale(config('app.locale'));
    }

    
}
