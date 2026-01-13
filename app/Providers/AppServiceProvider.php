<?php

namespace App\Providers;

use App\Models\AssetAssignment;
use App\Observers\AssetAssignmentObserver;
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
        Carbon::setLocale(config('app.locale'));
    }

    
}
