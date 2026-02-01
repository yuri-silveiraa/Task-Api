<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Services\TaskService;
use Illuminate\Http\Request;

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
}
