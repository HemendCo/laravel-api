<?php

namespace Hemend\Api;

use Illuminate\Support\ServiceProvider;

use Hemend\Api\Providers\ConsoleServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    
    /**
     * Booting the package.
     */
    public function boot()
    {
        $this->app->register(ConsoleServiceProvider::class);

        $configPath = __DIR__ . '/../config/config.php';

        $this->mergeConfigFrom($configPath, 'api');

        $this->publishes([
            $configPath => config_path('api.php'),
        ], 'config');
    }
    
    /**
     * Register the service provider.
     */
    public function register()
    {

    }
}
