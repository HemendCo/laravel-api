<?php

namespace Hemend\Api\Providers;

use Hemend\Api\Foundation\Dispatcher;
use Hemend\Api\Libraries\JobUpdater;
use Illuminate\Contracts\Bus\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Bus\QueueingDispatcher as QueueingDispatcherContract;
use Illuminate\Contracts\Queue\Factory as QueueFactoryContract;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;


class JobServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    public function boot()
    {
        $this->bootListeners();
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(Dispatcher::class, function ($app) {
            return new Dispatcher($app, function ($connection = null) use ($app) {
                return $app[QueueFactoryContract::class]->connection($connection);
            }, app(JobUpdater::class));
        });
        $this->app->alias(
            Dispatcher::class,
            DispatcherContract::class
        );
        $this->app->alias(
            Dispatcher::class,
            QueueingDispatcherContract::class
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Dispatcher::class,
            DispatcherContract::class,
            QueueingDispatcherContract::class,
        ];
    }

    private function bootListeners()
    {
        /** @var EventManager $eventManager */
        $eventManager = app(config('api.job.event_manager'));

        // Add Event listeners
        app(QueueManager::class)->before(function (JobProcessing $event) use ($eventManager) {
          $eventManager->before($event);
        });
        app(QueueManager::class)->after(function (JobProcessed $event) use ($eventManager) {
          $eventManager->after($event);
        });
        app(QueueManager::class)->failing(function (JobFailed $event) use ($eventManager) {
          $eventManager->failing($event);
        });
        app(QueueManager::class)->exceptionOccurred(function (JobExceptionOccurred $event) use ($eventManager) {
          $eventManager->exceptionOccurred($event);
        });
    }
}