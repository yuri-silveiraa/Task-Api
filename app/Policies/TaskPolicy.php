<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return true; // qualquer usuário autenticado
    }

    public function create(User $user)
    {
        return $user->isManager();
    }

    public function update(User $user, Task $task)
    {
        return $user->isManager() && $task->user_id === $user->id;
    }

    public function delete(User $user, Task $task)
    {
        return false; // só admin, o before ja cobre
    }
}
