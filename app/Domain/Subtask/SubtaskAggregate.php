<?php
namespace App\Domain\Subtask;

use App\Domain\Subtask\Events\SubtaskAdded;
use App\Domain\Subtask\Events\SubtaskCompleted;
use App\Domain\Subtask\Events\SubtaskUncompleted;
use App\Domain\Subtask\Events\SubtaskDeleted;
use App\Domain\Subtask\Events\SubtasksDeleted;
use App\Domain\Task\TaskAggregate;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class SubtaskAggregate extends AggregateRoot
{

    public function addSubtask(string $taskId, string $title): self
    {
        $this->validateTask($taskId);
        $this->validateMaxSubtasks($taskId);

        $this->recordThat(new SubtaskAdded(
            subtaskUuid: $this->uuid(),
            taskId: $taskId,
            title: $title,
            createdAt: now()
        ));

        return $this;
    }

    public function toggleCompletion(bool $completed): self
    {
        $this->validateSubtask($this->uuid());

        $event = $completed
            ? new SubtaskCompleted($this->uuid())
            : new SubtaskUncompleted($this->uuid());

        $this->recordThat($event);
        return $this;
    }

    private function validateSubtask(string $id): void
    {
        $Subtask = \App\Models\Subtask::find($id);

        if (!$Subtask) {
            throw new \DomainException("Subtask does not exist");

        }

    }

    private function validateTask(string $taskId): void
    {
        $task = \App\Models\Task::find($taskId);

        if (!$task) {
            throw new \DomainException("Parent task not found");
        }

        if ($task->status === 'completed') {
            throw new \DomainException("Cannot add subtask to completed task");
        }
    }

    private function validateMaxSubtasks(string $taskId): void
    {
        $count = \App\Models\Subtask::where('task_id', $taskId)->count();

        if ($count >= 5) {
            throw new \DomainException("Maximum of 5 subtasks per task exceeded");
        }
    }

    public function deleteSubtask(): self
    {
        $this->recordThat(new SubtaskDeleted($this->uuid()));
        return $this;
    }

}
