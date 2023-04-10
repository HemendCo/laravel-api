<?php

namespace Hemend\Api\Providers;

use Illuminate\Support\ServiceProvider;

use Hemend\Api\Commands\AclPermissionsCollect;
use Hemend\Api\Commands\ApiBasic;
use Hemend\Api\Commands\ApiMaker;
use Hemend\Api\Commands\ApiMethodMaker;
use Hemend\Api\Commands\ApiServiceMaker;
use Hemend\Api\Commands\ApiVersionCopy;
use Hemend\Api\Commands\ApiVersionMaker;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * The available commands
     *
     * @var array
     */
    protected $commands = [
        AclPermissionsCollect::class,
        ApiBasic::class,
        ApiMaker::class,
        ApiMethodMaker::class,
        ApiServiceMaker::class,
        ApiVersionCopy::class,
        ApiVersionMaker::class,
    ];

    /**
     * Register the commands.
     */
    public function register()
    {
        $this->commands($this->commands);
    }

    /**
     * @return array
     */
    public function provides()
    {
        $provides = $this->commands;

        return $provides;
    }
}
