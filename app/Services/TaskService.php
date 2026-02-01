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
}
