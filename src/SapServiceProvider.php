<?php

namespace Elielelie\Sap;

use Elielelie\Sap\Helpers\Arr;
use Elielelie\Sap\Helpers\Guid;
use Illuminate\Support\ServiceProvider;

class SapServiceProvider extends  ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/sap.php' => config_path('sap.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__ . '/../config/sap.php', 'sap'
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('sap', function ($app) {
            return new Sap();
        });

        $this->app->singleton('guid', function ($app) {
            return new Guid();
        });

        $this->app->singleton('sarr', function ($app) {
            return new Arr();
        });
    }
}