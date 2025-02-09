<?php

namespace App\Domain\Task;

use App\Models\{Subtask, Task};
use App\Domain\Subtask\SubtaskAggregate;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;
use App\Domain\Task\Events\{TaskCreated, SubtaskCompletionToggled, TaskDeleted, TaskStatusUpdated};


class TaskAggregate extends AggregateRoot
{
    public string $title;
    public string $status = 'not_started';
    public array $subtasks = [];

    public function createTask(string $title): self
    {
        $this->recordThat(new TaskCreated($title));
        return $this;
    }

    public function changeTaskStatue(): self
    {
        $this->validateTask($this->uuid());
        $this->updateStatus();
        return $this;
    }

    public function toggleSubtaskCompletion(string $subtaskId, bool $completed): self
    {
        if (!Subtask::with('subtasks')->contains('id', $subtaskId)) {
            throw new \Exception('Subtask not found.');
        }
        $this->recordThat(new SubtaskCompletionToggled($subtaskId, $completed));
        return $this;
    }

    private function updateStatus(): void
    {
        $task = Task::where('id', $this->uuid())->first();
        $subtasks = Subtask::where('task_id', $this->uuid())->get();
        $total = $subtasks->count();
        $completed = $subtasks->where('completed', true)->count();

        $newStatus = match (true) {
            $completed === 0 => 'not_started',
            $completed === $total => 'completed',
            $completed > 0 && $completed < $total => 'in_progress',
            default => 'not_started',
        };

        if ($newStatus !== $task->status) {
            $this->recordThat(new TaskStatusUpdated($newStatus));
        }
    }

    public function deleteTask(string $taskId): self
    {
        $this->validateTask($taskId, "delete");
        $this->recordThat(new TaskDeleted($taskId));
        return $this;
    }


    private function validateTask(string $taskId, string $action =  "status_change"): void
    {
        $task = Task::find($taskId);

        if ($action == "status_change") {
            if (!$task) {
                throw new \DomainException("Parent task not found");
            }

            if ($task->status === 'completed') {
                throw new \DomainException("Cannot add subtask to completed task");
            }
        }


        if ($action == "delete") {
            if ($task->status === 'completed') {
                throw new \DomainException("Cannot delete completed task");
            }
        }


    }
}
