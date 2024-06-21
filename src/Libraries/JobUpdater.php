<?php

namespace Hemend\Api\Libraries;

use Hemend\Api\Interfaces\TrackableJob;
use Hemend\Api\Models\JobsTrackers;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Log;

class JobUpdater
{
  public function update($job, array $data)
  {
    if ($this->isEvent($job)) {
      $this->updateEvent($job, $data);
    }

    $this->updateJob($job, $data);
  }

  /**
   * @param JobProcessing|JobProcessed|JobFailed|JobExceptionOccurred $event
   */
  protected function updateEvent($event, array $data)
  {
    $job = $this->parseJob($event);
    $jobStatus = $this->getJobTracker($job);

    if (!$jobStatus) {
      return;
    }

    try {
      $data['attempts'] = $event->job->attempts();
    } catch (\Throwable $e) {
      try {
        $data['attempts'] = $job->attempts();
      } catch (\Throwable $e) {
        Log::error($e->getMessage());
      }
    }

    if ($jobStatus->isFailed
      && isset($data['status'])
      && $data['status'] === $jobStatus::STATUS_FINISHED
    ) {
      unset($data['status']);
    }

    $jobStatus->update($data);
  }

  protected function updateJob($job, array $data)
  {
    if ($jobStatus = $this->getJobTracker($job)) {
      $jobStatus->update($data);
    }
  }

  /**
   * @param  JobProcessing|JobProcessed|JobFailed|JobExceptionOccurred $event
   * @return mixed|null
   */
  protected function parseJob($event)
  {
    try {
      $payload = $event->job->payload();

      return unserialize($payload['data']['command']);
    } catch (\Throwable $e) {
      Log::error($e->getMessage());

      return null;
    }
  }

  protected function getJobTrackerId($job)
  {
    try {
      if ($job instanceof TrackableJob) {
        return $job->getJobTrackerId();
      }
    } catch (\Throwable $e) {
      Log::error($e->getMessage());

      return null;
    }

    return null;
  }

  protected function getJobTracker($job)
  {
    if ($id = $this->getJobTrackerId($job)) {
      /** @var JobsTrackers $entityClass */
      $entityClass = app(config('api.job.model'));

      return $entityClass::on(config('api.job.db_connection'))->whereKey($id)->first();
    }

    return null;
  }

  protected function isEvent($job)
  {
    return $job instanceof JobProcessing
      || $job instanceof JobProcessed
      || $job instanceof JobFailed
      || $job instanceof JobExceptionOccurred;
  }
}