<?php

namespace chaos613\UpsVoid;

use Illuminate\Support\ServiceProvider;
use YourVendorName\UpsVoid\Http\Clients\UpsClient;

class UpsVoidServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // Merge package configuration with user's configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/ups-void.php', 'ups-void'
        );

        // Bind the main UpsVoid class to the service container
        $this->app->singleton(UpsVoid::class, function ($app) {
            // Resolve the Guzzle HTTP client and configuration
            $config = $app['config']->get('ups-void');
            $httpClient = new \GuzzleHttp\Client(['base_uri' => $config['base_uri']]);

            // Create and return the UpsClient instance
            return new UpsClient($httpClient, $config);
        });

        // Register the UpsVoid alias
        $this->app->alias(UpsVoid::class, 'ups-void');
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the configuration file
        $this->publishes([
            __DIR__ . '/../config/ups-void.php' => config_path('ups-void.php'),
        ], 'config');
    }
}

