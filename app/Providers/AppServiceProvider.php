<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Ory\Hydra\Client\Api\AdminApi;
use Ory\Hydra\Client\Api\PublicApi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PublicApi::class, function () {
            return tap(new PublicApi(), function (PublicApi $instance) {
                $instance->getConfig()
                    ->setHost('http://127.0.0.1:4444')
                    ->setUsername('my-rp')
                    ->setPassword('my-secret')
                    ->setAccessToken(null);
            });
        });

        $this->app->singleton(AdminApi::class, function () {
            return tap(new AdminApi(), function (AdminApi $instance) {
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
