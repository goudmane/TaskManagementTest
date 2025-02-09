<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subtask extends Model
{
    use HasUuids, HasFactory;
    protected $fillable = ['title', 'task_id', 'completed'];

    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
