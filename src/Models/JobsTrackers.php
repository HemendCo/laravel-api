<?php

namespace Hemend\Api\Models;

use Hemend\Api\TypeHint\JobTrackerStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * @property int    $id
 * @property string $job_id
 * @property string $type
 * @property string $queue
 * @property int    $attempts
 * @property int    $progress_now
 * @property int    $progress_max
 * @property string $status
 * @property string $input
 * @property string $output
 * @property string $executed_at
 * @property string $started_at
 * @property string $finished_at
 * @property string $created_at
 * @property string $updated_at
 * @property bool  $is_ended
 * @property bool  $is_executing
 * @property bool  $is_failed
 * @property bool  $is_finished
 * @property bool  $is_queued
 * @property bool  $is_retrying
 * @method static Builder|JobsTrackers whereAttempts($value)
 * @method static Builder|JobsTrackers whereCreatedAt($value)
 * @method static Builder|JobsTrackers whereFinishedAt($value)
 * @method static Builder|JobsTrackers whereId($value)
 * @method static Builder|JobsTrackers whereInput($value)
 * @method static Builder|JobsTrackers whereJobId($value)
 * @method static Builder|JobsTrackers whereOutput($value)
 * @method static Builder|JobsTrackers whereProgressMax($value)
 * @method static Builder|JobsTrackers whereProgressNow($value)
 * @method static Builder|JobsTrackers whereQueue($value)
 * @method static Builder|JobsTrackers whereStartedAt($value)
 * @method static Builder|JobsTrackers whereStatus($value)
 * @method static Builder|JobsTrackers whereType($value)
 */
class JobsTrackers extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'progress_percentage',
        'is_ended',
        'is_finished',
        'is_failed',
        'is_executing',
        'is_queued',
        'is_retrying',
        'is_retrying',
    ];

    public function getProgressPercentageAttribute(): float|int
    {
        return $this->progress_max !== 0 ? round(100 * $this->progress_now / $this->progress_max) : 0;
    }

    public function getIsEndedAttribute(): bool
    {
        return in_array($this->status, [JobTrackerStatus::FAILED->value, JobTrackerStatus::FINISHED->value], true);
    }

    public function getIsFinishedAttribute(): bool
    {
        return $this->status === JobTrackerStatus::FINISHED->value;
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === JobTrackerStatus::FAILED->value;
    }

    public function getIsExecutingAttribute(): bool
    {
        return $this->status === JobTrackerStatus::EXECUTING->value;
    }

    public function getIsQueuedAttribute(): bool
    {
        return $this->status === JobTrackerStatus::QUEUED->value;
    }

    public function getIsRetryingAttribute(): bool
    {
        return $this->status === JobTrackerStatus::RETRYING->value;
    }

    public static function getAllowedStatuses(): array
    {
        return [
            JobTrackerStatus::QUEUED,
            JobTrackerStatus::EXECUTING,
            JobTrackerStatus::FINISHED,
            JobTrackerStatus::FAILED,
            JobTrackerStatus::RETRYING,
        ];
    }
}
