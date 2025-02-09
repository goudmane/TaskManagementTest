<?php

namespace App\Domain\Task\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TaskCreated extends ShouldBeStored
{
    public function __construct(
        public readonly string $title
    ) {}
}
