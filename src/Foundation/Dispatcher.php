<?php

namespace Hemend\Api\Foundation;

use Closure;
use Carbon\Carbon;
use Hemend\Api\Libraries\JobUpdater;
use Illuminate\Bus\Dispatcher as BusDispatcher;
use Illuminate\Contracts\Container\Container;

class Dispatcher extends BusDispatcher
{
  /**
   * @var JobUpdater
   */
  private $updater;

  public function __construct(Container $container, Closure $queueResolver, JobUpdater $updater)
  {
    $this->updater = $updater;

    parent::__construct($container, $queueResolver);
  }

  public function dispatch($command)
  {
    $result = parent::dispatch($command);

    $executed_at = null;
    if($command->delay) {
      if($command->delay instanceof Carbon) {
        $executed_at = Carbon::parse($command->delay)->toDateTimeString();
      } else {
        $executed_at = now()->addSeconds($command->delay)->toDateTimeString();
      }
    }

    $this->updater->update($command, [
      'job_id' => $result,
      'queue' => $command->queue ?? 'default',
      'executed_at' => $executed_at
    ]);

    return $result;
  }
}