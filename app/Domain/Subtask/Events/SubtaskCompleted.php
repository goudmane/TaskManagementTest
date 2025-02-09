<?php

namespace App\Domain\Subtask\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SubtaskCompleted extends ShouldBeStored
{
    public function __construct(
        public readonly string $subtaskUuid,
    ) {}
}
