<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\RolePermissions;

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
        RolePermissions::register();
    }
}
