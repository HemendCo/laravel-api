<?php

namespace Hemend\Api;

use Hemend\Api\Providers\ConsoleServiceProvider;
//use Hemend\Api\Providers\SeedServiceProvider;
use Illuminate\Support\ServiceProvider;


class ApiServiceProvider extends ServiceProvider
{
    
    /**
     * Booting the package.
     */
    public function boot()
    {
        $this->app->register(ConsoleServiceProvider::class);
//        $this->app->register(SeedServiceProvider::class);

        $configPath = __DIR__ . '/../config/config.php';

        $this->mergeConfigFrom($configPath, 'api');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $configPath => config_path('api.php'),
            ], ['config', 'api']);

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], ['migrations', 'api']);

            $this->publishes([
                __DIR__.'/../database/seeders/' => database_path('seeders'),
            ], ['seeders', 'api']);

            $this->publishes([
                __DIR__.'/../models' => app_path('Models'),
            ], ['models', 'api']);
        }
    }
    
    /**
     * Register the service provider.
     */
    public function register()
    {

    }
}
