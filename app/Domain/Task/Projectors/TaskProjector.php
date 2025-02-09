<?php

namespace App\Domain\Task\Projectors;

use App\Domain\Subtask\SubtaskAggregate;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use App\Domain\Task\Events\{TaskCreated, SubtaskAdded, SubtaskCompletionToggled, TaskDeleted, TaskStatusUpdated};
use App\Models\{Task, Subtask};
use Illuminate\Support\Facades\Log;
use Spatie\EventSourcing\EventHandlers\Projectors\ProjectsEvents;

class TaskProjector extends Projector
{

    public function onTaskCreated(TaskCreated $event)
    {
        Task::create([
            'id' => $event->aggregateRootUuid(),
            'title' => $event->title,
            'status' => 'not_started',
        ]);
    }

    public function onTaskStatusUpdated(TaskStatusUpdated $event)
    {
        Task::where('id', $event->aggregateRootUuid())->update(['status' => $event->status]);
    }

    public function onTaskDeleted(TaskDeleted $event)
    {
        $taskId = $event->aggregateRootUuid();
        $subtasks = Subtask::where('task_id', $taskId)->get();

        foreach ($subtasks as $subtask) {
            SubtaskAggregate::retrieve($subtask->id)
                ->deleteSubtask()
                ->persist();
        }

        Task::where('id', $taskId)->delete();
    }
}
