<?php

namespace Hemend\Api\TypeHint;

enum JobTrackerStatus: string
{
    case QUEUED = 'Queued';
    case EXECUTING = 'Executing';
    case FINISHED = 'Finished';
    case FAILED = 'Failed';
    case RETRYING = 'Retrying';
}
