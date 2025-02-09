
<div class="max-w-2xl mx-auto mt-8">
    @if (session()->has('error'))
        <div class="bg-white rounded-lg shadow p-6 mb-4">
            <p class="text-red-500 text-sm mb-4">{{ session('error') }}</p>
        </div>
    @endif
    @foreach ($tasks as $task)
        <div class="bg-white rounded-lg shadow p-6 mb-4" wire:key="task-{{ $task->id }}">
            <h3 class="text-lg font-semibold">{{ $task->title }}</h3>
            <p class="text-sm text-gray-600">Status:
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @match($task->status) {
                        'not_started' { bg-yellow-100 text-yellow-800 }
                        'in_progress' { bg-blue-100 text-blue-800 }
                        'completed' { bg-green-100 text-green-800 }
                        default { bg-gray-100 text-gray-800 }
                    }">
                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                </span>
            </p>
            <ul class="mt-4 space-y-2">
                @foreach ($task->subtasks as $subtask)
                    <li class="flex items-center group" wire:key="subtask-{{ $subtask->id }}">
                        <input
                            type="checkbox"
                            wire:click="toggleSubtask('{{ $subtask->id }}')"
                            class="mr-2"
                            {{ $subtask->completed ? 'checked' : '' }}
                            {{ $task->status === 'completed' ? 'disabled' : '' }}
                        >
                        <span class="{{ $subtask->completed ? 'line-through text-gray-400' : 'text-gray-800' }} flex-1">
                            {{ $subtask->title }}
                        </span>
                        @if ($task->status !== 'completed')
                            <button
                                type="button"
                                wire:click="deleteSubtask('{{ $subtask->id }}')"
                                class="text-red-500 hover:text-red-700 ml-2 transition-opacity"
                                aria-label="Delete subtask"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        @endif
                    </li>
                @endforeach
            </ul>
            @if (count($task->subtasks) < 5)
                <button wire:click="startAddSubtask('{{ $task->id }}', '{{ $task->title }}')"
                    class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Add Subtask
                </button>
            @endif
            <button wire:click="deleteTask('{{ $task->id }}')" class="mt-4 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                Delete Task
            </button>
        </div>
    @endforeach
</div>

<?php
    use function Livewire\Volt\{state, on};

    $fetchTasks = function () {
        return \App\Models\Task::with('subtasks')->get();
    };


    state([
        'tasks' =>  $fetchTasks,
        'activeTaskId' => null,
        'newSubtaskTitle' => ''
    ]);

    on(['taskUpdated' => function () {
        $this->tasks = $this->fetchTasks();
    }]);

    on(['createSubtask' => function ($subTaskTitle) {
        $this->newSubtaskTitle = $subTaskTitle;
        $this->addSubtask();
    }]);


    $startAddSubtask = function ($taskId, $taskTitle) {
        $this->dispatch('switchToSubtaskMode', $taskTitle);
        $this->activeTaskId = $taskId;
    };

    $addSubtask = function () {
        $this->validate(['newSubtaskTitle' => 'required|string|max:255']);

        try {
            $subtaskUuid = \Illuminate\Support\Str::uuid();

            \App\Domain\Subtask\SubtaskAggregate::retrieve($subtaskUuid)
                ->addSubtask($this->activeTaskId, $this->newSubtaskTitle)
                ->persist();

            $this->tasks = \App\Models\Task::with('subtasks')->get();
            $this->resetInputs();
        } catch (\DomainException $e) {
            session()->flash('error', $e->getMessage());
        }
    };

    $toggleSubtask = function ($subtaskId) {
        try {
            $subtask = \App\Models\Subtask::findOrFail($subtaskId);

            \App\Domain\Subtask\SubtaskAggregate::retrieve($subtaskId)
                ->toggleCompletion(!$subtask->completed)
                ->persist();

            $this->tasks = \App\Models\Task::with('subtasks')->get();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    };

    $resetInputs = function () {
        $this->newSubtaskTitle = '';
    };

    $deleteTask = function ($taskId) {
        try {
            \App\Domain\Task\TaskAggregate::retrieve($taskId)->deleteTask($taskId)->persist();
            $this->tasks = \App\Models\Task::with('subtasks')->get();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    };

    $deleteSubtask = function ($subtaskUuid) {
        try {
            \App\Domain\Subtask\SubtaskAggregate::retrieve($subtaskUuid)
                ->deleteSubtask($subtaskUuid)
                ->persist();

            $this->dispatch('taskUpdated');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    };

?>
