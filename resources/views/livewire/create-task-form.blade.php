
<form wire:submit="createTask" class="mb-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
        {{ $formTitle }}
        <input type="text" wire:model="title" placeholder="Enter {{ strtolower($for) }} title" class="w-full border rounded px-3 py-2">
        @error('title')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
        <button type="submit" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Create {{ $for }}
        </button>
        @if ($isSubtaskMode)
            <button wire:click="backToTask" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Back to tasks
            </button>
        @endif
    </div>
</form>

<?php
    use function Livewire\Volt\{state, rules, on};

    state([
        'title' => '',
        'for' => 'Task',
        'formTitle' => 'Create New Task',
        'isSubtaskMode' => false,
    ]);
    rules([
        'title' => 'required|string|max:255',
    ]);


    on(['switchToSubtaskMode' => function ($taskTitle) {
        $this->isSubtaskMode = true;
        $this->for = 'Subtask';

        $this->formTitle = 'Create New Subtask Under '.$taskTitle;
    }]);

    $backToTask = function () {
        $this->isSubtaskMode = false;
        $this->for = 'Task';
    };

    $createTask = function () {
        $this->validate();

        if ($this->isSubtaskMode) {

            $this->dispatch('createSubtask', $this->title);
        } else {

            $taskUuid = \Illuminate\Support\Str::uuid();
            \App\Domain\Task\TaskAggregate::retrieve($taskUuid)
                ->createTask($this->title)
                ->persist();

            $this->dispatch('taskUpdated');
        }

        $this->title = '';
    };



?>
