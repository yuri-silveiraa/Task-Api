<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Services\TaskService;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {}

    public function index()
    {
        return response()->json(
            $this->taskService->list(),
            200
        );
    }

    public function store(TaskStoreRequest $request)
    {
        $task = $this->taskService->create(
            auth()->id(),
            $request->validated()
        );

        return response()->json($task, 201);
    }

    public function update(TaskUpdateRequest $request, int $id)
    {
        $task = $this->taskService->update(
            $id,
            auth()->id(),
            $request->validated()
        );

        return response()->json($task, 200);
    }

    public function destroy(int $id)
    {
        $this->taskService->delete($id, auth()->id());

        return response()->json(null, 204);
    }
}
