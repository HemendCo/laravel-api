<?php

namespace Hemend\Api\Providers;

use Hemend\Api\Commands\AclPermissionsCollect;
use Hemend\Api\Commands\ApiBasic;
use Hemend\Api\Commands\ApiMaker;
use Hemend\Api\Commands\ApiEndpointMaker;
use Hemend\Api\Commands\ApiPackageMaker;
use Hemend\Api\Commands\ApiServiceMaker;
use Hemend\Api\Commands\ApiVersionMaker;

use Illuminate\Support\ServiceProvider;

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
    ApiEndpointMaker::class,
    ApiPackageMaker::class,
    ApiServiceMaker::class,
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
