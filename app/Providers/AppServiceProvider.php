<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\Unit;
use App\Models\Apartment;
use App\Observers\UnitObserver;
use App\Observers\ApartmentObserver;

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
        if (app()->environment('production')) {
        	URL::forceScheme('https');
        }

        // Register observers for automatic property syncing
        Unit::observe(UnitObserver::class);
        Apartment::observe(ApartmentObserver::class);
    }
}
