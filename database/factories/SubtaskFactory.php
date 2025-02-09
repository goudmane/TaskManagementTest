<?php

namespace Database\Factories;

use App\Models\Subtask;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubtaskFactory extends Factory
{
    protected $model = Subtask::class;

    public function definition()
    {
        return [
            'task_id' => \App\Models\Task::factory(),
            'title' => $this->faker->sentence,
            'completed' => $this->faker->boolean,
        ];
    }
}
