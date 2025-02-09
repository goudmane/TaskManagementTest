<?php

namespace App\Domain\Task\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SubtaskCompletionToggled extends ShouldBeStored
{
    public function __construct(
        public readonly string $subtaskId,
        public readonly bool $completed
    ) {}
}
