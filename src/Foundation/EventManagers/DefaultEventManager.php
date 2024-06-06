<?php

namespace Hemend\Api\Foundation\EventManagers;

use Carbon\Carbon;
use Hemend\Api\TypeHint\JobTrackerStatus;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

class DefaultEventManager extends EventManager
{
    public function before(JobProcessing $event): void
    {
        $this->getUpdater()->update($event, [
            'status' => JobTrackerStatus::EXECUTING->value,
            'job_id' => $event->job->getJobId(),
            'queue' => $event->job->getQueue(),
            'started_at' => Carbon::now(),
        ]);
    }

    public function after(JobProcessed $event): void
    {
        if (!$event->job->hasFailed()) {
            $this->getUpdater()->update($event, [
                'status' => JobTrackerStatus::FINISHED->value,
                'finished_at' => Carbon::now(),
            ]);
        }
    }

    public function failing(JobFailed $event): void
    {
        $this->getUpdater()->update($event, [
            'status' => ($event->job->attempts() !== $event->job->maxTries())
              ? JobTrackerStatus::FAILED->value
              : JobTrackerStatus::RETRYING->value,
            'finished_at' => Carbon::now(),
        ]);
    }

    public function exceptionOccurred(JobExceptionOccurred $event): void
    {
        $this->getUpdater()->update($event, [
            'status' => ($event->job->attempts() >= $event->job->maxTries())
              ? JobTrackerStatus::FAILED->value
              : JobTrackerStatus::RETRYING->value,
            'finished_at' => Carbon::now(),
        ]);
    }
}
