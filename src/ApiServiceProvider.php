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
    }
    
    /**
     * Register the service provider.
     */
    public function register()
    {

    }
}
