<?php

namespace App\Domain\Task\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TaskStatusUpdated extends ShouldBeStored
{
    public function __construct(
        public readonly string $status
    ) {}
}
