<?php

namespace Hemend\Api\Providers;

use Hemend\Library\Glob;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any app services.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(['Illuminate\Console\Events\CommandStarting', 'Illuminate\Console\Events\CommandFinished'], function ($event) {
          if ($event->command == 'passport:install') {
            $migrationPath = database_path('migrations');
            foreach(Glob::recursive($migrationPath, '*_create_oauth_*.php') as $file) {
              unlink($file);
            }
          }
        });
    }
}