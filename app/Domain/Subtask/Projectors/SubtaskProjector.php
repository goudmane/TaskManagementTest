<?php

namespace App\Domain\Subtask\Projectors;

use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use App\Domain\Subtask\Events\{SubtaskAdded, SubtaskCompleted, SubtaskUncompleted, SubtaskDeleted};
use App\Domain\Task\TaskAggregate;
use App\Models\Subtask;
use Illuminate\Support\Facades\Log;

class SubtaskProjector extends Projector
{

    public function onSubtaskAdded(SubtaskAdded $event)
    {
        Subtask::create([
            'id' => $event->subtaskUuid,
            'task_id' => $event->taskId,
            'title' => $event->title,
            'completed' => false,
        ]);
    }

    public function onSubtaskCompleted(SubtaskCompleted $event)
    {
        $this->updateSubtaskStatus($event->subtaskUuid, true);
    }

    public function onSubtaskUncompleted(SubtaskUncompleted $event)
    {
        $this->updateSubtaskStatus($event->subtaskUuid, false);
    }

    protected function updateSubtaskStatus(string $subtaskUuid, bool $completed)
    {
        try {
            $subtask = Subtask::where('id', $subtaskUuid)->first();
            $taskUuid = $subtask->task_id;
            $subtask->update(['completed' => $completed]);
            $this->triggerTaskStatusUpdate($taskUuid);
        } catch (\Exception $e) {
            Log::error("Subtask status update failed: {$e->getMessage()}", [
                'subtask_uuid' => $subtaskUuid,
                'completed' => $completed
            ]);
        }
    }

    protected function triggerTaskStatusUpdate(string $taskUuid)
    {
        TaskAggregate::retrieve($taskUuid)
                ->changeTaskStatue()
                ->persist();
    }

    public function onSubtaskDeleted(SubtaskDeleted $event)
    {
        Subtask::where('id', $event->subtaskUuid)
            ->delete();
    }


}
