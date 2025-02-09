<?php

namespace App\Domain\Subtask\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SubtaskAdded extends ShouldBeStored
{
    public function __construct(
        public readonly string $subtaskUuid,
        public readonly string $taskId,
        public readonly string $title,
        public readonly string $createdAt
    ) {}
}
