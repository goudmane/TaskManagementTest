<?php

namespace App\Domain\Subtask\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SubtaskDeleted extends ShouldBeStored
{
    public function __construct(
        public readonly string $subtaskUuid,
    ) {}
}
