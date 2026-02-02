<?php

namespace App\Services;

use App\Models\Task;

class TaskService
{
    public function list()
    {
        return Task::where('user_id', auth()->id())->get();
    }

    public function create(int $userId, array $data)
    {
        return Task::create([
            'user_id' => $userId,
            ...$data
        ]);
    }
    
     public function update(int $taskId, int $userId, array $data)
    {
        $task = Task::where('id', $taskId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $task->update($data);

        return $task;
    }

    public function delete(int $taskId, int $userId): void
    {
        $task = Task::where('id', $taskId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $task->delete();
    }
}
