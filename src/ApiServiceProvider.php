<?php

namespace Hemend\Api;

use Hemend\Api\Providers\AppServiceProvider;
use Hemend\Api\Providers\AuthServiceProvider;
use Hemend\Api\Providers\ConsoleServiceProvider;
use Hemend\Api\Providers\RouteServiceProvider;
use Hemend\Api\Providers\JobServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    
    /**
     * Booting the package.
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        $this->app->register(AppServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(ConsoleServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(JobServiceProvider::class);

        $configPath = __DIR__ . '/../config/config.php';
        $permissionConfigPath = __DIR__ . '/../config/permission.php';

        $this->mergeConfigFrom($configPath, 'api');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $configPath => config_path('api.php'),
                $permissionConfigPath => config_path('permission.php'),
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

            $this->publishes([
              __DIR__.'/../lang/' => lang_path(),
            ], ['lang', 'api']);
        }
    }
    
    /**
     * Register the service provider.
     */
    public function register()
    {

    }
}
