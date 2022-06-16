<?php

namespace Hemend\Api\Providers;

use Hemend\Api\Foundation\RouteRegistrar;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $routes = config('api.routes');
        $options = ['prefix' => 'api'];

        if(is_array($routes) || (is_scalar($routes) && $routes == '*')) {
            Route::group($options, function ($router) use($routes) {
                $registrar = new RouteRegistrar($router);
                $registrar->use($routes);
            });
        }
    }
}