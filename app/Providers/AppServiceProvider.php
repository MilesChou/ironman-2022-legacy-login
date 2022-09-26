<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Ory\Hydra\Client\Api\AdminApi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AdminApi::class, function () {
            return tap(new AdminApi(), function ($instance) {
                $instance->getConfig()->setHost('http://127.0.0.1:4445');
            });
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
