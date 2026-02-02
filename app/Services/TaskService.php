<?php

namespace App\Services;

use App\Models\Task;

class TaskService
{
    public function list()
    {
        return Task::all();
    }

    public function create(int $userId, array $data)
    {
        return Task::create([
            'user_id' => $userId,
            ...$data
        ]);
    }
    
     public function update(Task $task, array $data)
    {
        $task->update($data);

        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}
