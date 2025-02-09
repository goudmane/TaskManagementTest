<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasUuids, HasFactory;
    protected $fillable = ['title', 'status'];

    public function subtasks()
    {
        return $this->hasMany(Subtask::class)->orderBy('created_at');
    }
}
