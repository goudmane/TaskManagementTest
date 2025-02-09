<?php

use Illuminate\Support\Facades\Event;
use App\Domain\Task\Events\TaskCreated;
use Livewire\Livewire;
use App\Models\Task;
use App\Models\Subtask;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);


it('can create task', function () {
    Livewire::test('create-task-form')
        ->set('title', 'New Task')
        ->call('createTask')
        ->assertHasNoErrors();

    expect(Task::where('title', 'New Task')->exists())->toBeTrue();
});

it('requires task title', function () {
    Livewire::test('create-task-form')
        ->set('title', '')
        ->call('createTask')
        ->assertHasErrors(['title' => 'required']);
});

it('can add subtasks', function () {
    $task = Task::factory()->create();

    Livewire::test('task-list')
        ->set(['newSubtaskTitle' =>  'New Subtask', 'activeTaskId' =>  $task->id])
        ->call('addSubtask')
        ->assertHasNoErrors();

    expect(Subtask::where('title', 'New Subtask')->exists())->toBeTrue();
});

it('prevents over 5 subtasks', function () {
    $task = Task::factory()
        ->has(Subtask::factory()->count(5))
        ->create();

    Livewire::test('task-list')
        ->call('startAddSubtask', $task->id, $task->title)
        ->set(['newSubtaskTitle' =>  'Sixth Subtask', 'activeTaskId' =>  $task->id])
        ->call('addSubtask');

    expect(Subtask::where('title', 'Sixth Subtask')->exists())->toBeFalse();
});

it('can toggle subtask completion', function () {
    $subtask = Subtask::factory()->create(['completed' => false]);

    Livewire::test('task-list')
        ->call('toggleSubtask', $subtask->id)
        ->assertHasNoErrors();

    expect($subtask->refresh()->completed)->toBeTrue();
});

it('toggles subtask completion', function () {
    $task = Task::factory()
        ->has(Subtask::factory()->count(1))
        ->create();

    $subtask = $task->subtasks->first();

    Livewire::test('task-list')
        ->call('toggleSubtask', $subtask->id)
        ->assertOk();
});

it('deletes a task successfully', function () {
    $task = Task::factory()->create();

    Livewire::test('task-list')
        ->call('deleteTask', $task->id)
        ->assertOk();

    expect(Task::find($task->id))->toBeNull();
});

it('deletes a subtask successfully', function () {
    $subtask = Subtask::factory()->create();

    Livewire::test('task-list')
        ->call('deleteSubtask', $subtask->id)
        ->assertOk();

    expect(Subtask::find($subtask->id))->toBeNull();
});

it('can change task status based on subtask completion', function () {
    $task = Task::factory()
        ->has(Subtask::factory()->count(3))
        ->create();

    expect($task->refresh()->status)->toBe('not_started');

    $subtask = $task->subtasks->first();
    Livewire::test('task-list')
        ->call('toggleSubtask', $subtask->id)
        ->assertHasNoErrors();

    expect($task->refresh()->status)->toBe('in_progress');

    foreach ($task->subtasks as $subtask) {
        Livewire::test('task-list')
            ->call('toggleSubtask', $subtask->id)
            ->assertHasNoErrors();
    }

    expect($task->refresh()->status)->toBe('completed');
});
