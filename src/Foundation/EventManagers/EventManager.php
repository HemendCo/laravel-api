<?php

namespace Hemend\Api\Foundation\EventManagers;

use Hemend\Api\Libraries\JobUpdater;
use Hemend\Api\Models\JobsTrackers;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

abstract class EventManager
{
    abstract public function before(JobProcessing $event): void;

    abstract public function after(JobProcessed $event): void;

    abstract public function failing(JobFailed $event): void;

    abstract public function exceptionOccurred(JobExceptionOccurred $event): void;

    /**
     * @var JobUpdater
     */
    private $updater;

    private $entity;

    public function __construct(JobUpdater $updater)
    {
        $this->updater = $updater;
        $this->entity = app(config('api.job.model'));
    }

    /**
     * @return JobUpdater
     */
    protected function getUpdater()
    {
        return $this->updater;
    }

    /**
     * @return JobsTrackers
     */
    protected function getEntity()
    {
        return $this->entity;
    }
}
