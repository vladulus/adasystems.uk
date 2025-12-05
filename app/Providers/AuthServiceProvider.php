<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

// Modele
use App\Models\User;
use App\Models\Device;
use App\Models\Vehicle;
use App\Models\Driver;

// Politici
use App\Policies\UserPolicy;
use App\Policies\DevicePolicy;
use App\Policies\VehiclePolicy;
use App\Policies\DriverPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class    => UserPolicy::class,
        Device::class  => DevicePolicy::class,
        Vehicle::class => VehiclePolicy::class,
        Driver::class  => DriverPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
