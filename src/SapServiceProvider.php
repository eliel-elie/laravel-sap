<?php

namespace Elielelie\Sap;

use Elielelie\Sap\Commands\TestSapConnection;
use Elielelie\Sap\Helpers\{Arr, Guid};
use Illuminate\Support\ServiceProvider;

class SapServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/sap.php' => config_path('sap.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__ . '/../config/sap.php', 'sap'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                TestSapConnection::class,
            ]);
        }
    }

    /**
     * Register the service provider.
     */
    public function register(): void
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
