<?php

namespace App\Domain\Subtask\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SubtasksDeleted extends ShouldBeStored
{
    public function __construct(public string $taskId)
    {
    }
}
