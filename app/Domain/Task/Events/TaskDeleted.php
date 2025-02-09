<?php

namespace App\Domain\Task\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TaskDeleted extends ShouldBeStored
{
    public function __construct(
        public readonly string $taskId,
    ) {}
}
