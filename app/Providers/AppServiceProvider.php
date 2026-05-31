<?php

namespace App\Providers;

use App\Models\Motorcycle;
use App\Models\Rider;
use App\Policies\MotorcyclePolicy;
use App\Policies\RiderPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Motorcycle::class, MotorcyclePolicy::class);
        Gate::policy(Rider::class, RiderPolicy::class);

        // Super admin bypasses all gates
        Gate::before(function ($user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });
    }
}
