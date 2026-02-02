<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Task;
use App\Services\TaskService;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Task::class);

        return response()->json(
            $this->taskService->list(),
            200
        );
    }

    public function store(TaskStoreRequest $request)
    {
        $this->authorize('create', Task::class);

        $task = $this->taskService->create(
            auth()->id(),
            $request->validated()
        );

        return response()->json($task, 201);
    }

    public function update(TaskUpdateRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task = $this->taskService->update(
            $task,
            $request->validated()
        );

        return response()->json($task, 200);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $this->taskService->delete($task->id, auth()->id());

        return response()->json(null, 204);
    }
}
