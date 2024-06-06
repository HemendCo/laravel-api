<?php

namespace Hemend\Api\Traits;

use Hemend\Api\Libraries\JobUpdater;
use Hemend\Api\Models\JobsTrackers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

trait TrackableQueue
{
    /** @var int */
    protected int $trackerId;
    /** @var int */
    protected int $progressNow = 0;
    /** @var int */
    protected int $progressMax = 0;
    /** @var bool */
    protected bool $shouldTrack = true;

    public function setProgressMax($value)
    {
        $this->update(['progress_max' => $value]);
        $this->progressMax = $value;
    }

    public function setProgressNow($value, $every = 1)
    {
        if ($value % $every === 0 || $value === $this->progressMax) {
            $this->update(['progress_now' => $value]);
        }
        $this->progressNow = $value;
    }

    public function incrementProgress($offset = 1, $every = 1)
    {
        $value = $this->progressNow + $offset;
        $this->setProgressNow($value, $every);
    }

    protected function setInput(array $value)
    {
        $this->update(['input' => $value]);
    }

    public function setOutput(array $value)
    {
        $this->update(['output' => $value]);
    }

    protected function update(array $data)
    {
        /** @var JobUpdater $updater */
        $updater = app(JobUpdater::class);
        $updater->update($this, $data);
    }

    protected function prepareTracker(array $data = [])
    {
        if (!$this->shouldTrack) {
            return;
        }

        /** @var JobsTrackers $entityClass */
        $entityClass = app(config('api.job.model'));

        $data = array_merge(['type' => $this->getDisplayName()], $data);
        /** @var JobsTrackers $tracker */
        $tracker = $entityClass::query()->create($data);

        $this->trackerId = $tracker->getKey();
    }

    protected function getDisplayName(): string
    {
        return method_exists($this, 'displayName') ? $this->displayName() : static::class;
    }

    public function getJobTrackerId(): int|null
    {
        return $this->trackerId ? intval($this->trackerId) : null;
    }

    public function getJobTracker(): Model|JobsTrackers|null
    {
        if ($id = $this->getJobTrackerId()) {
            /** @var JobsTrackers $entityClass */
            $entityClass = app(config('api.job.model'));
            return $entityClass::on(config('api.job.db_connection'))
              ->whereKey($id)
              ->first();
        }

        return null;
    }
}
