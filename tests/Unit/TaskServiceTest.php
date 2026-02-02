<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_returns_all_tasks()
    {
        Task::factory()->count(3)->create();

        $service = new TaskService;
        $result = $service->list();

        $this->assertCount(3, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_create_task_with_valid_data()
    {
        $user = \App\Models\User::factory()->create();
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'completed' => false,
        ];

        $service = new TaskService;
        $result = $service->create($user->id, $taskData);

        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals('Test Task', $result->title);
        $this->assertEquals('Test Description', $result->description);
        $this->assertFalse($result->completed);
    }

    public function test_create_task_with_partial_data()
    {
        $user = \App\Models\User::factory()->create();
        $taskData = [
            'title' => 'Partial Task',
        ];

        $service = new TaskService;
        $result = $service->create($user->id, $taskData);

        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals('Partial Task', $result->title);
        $this->assertNull($result->description);
        $this->assertNull($result->completed); // Default value
    }

    public function test_update_task_with_valid_data()
    {
        $task = Task::factory()->create(['title' => 'Original Title']);
        $updateData = [
            'title' => 'Updated Task',
            'completed' => true,
        ];

        $service = new TaskService;
        $result = $service->update($task, $updateData);

        $this->assertSame($task, $result);
        $this->assertEquals('Updated Task', $result->title);
        $this->assertTrue($result->completed);
    }

    public function test_update_task_with_partial_data()
    {
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'description' => 'Original Description',
            'completed' => false,
        ]);
        $updateData = [
            'completed' => true,
        ];

        $service = new TaskService;
        $result = $service->update($task, $updateData);

        $this->assertSame($task, $result);
        $this->assertEquals('Original Title', $result->title); // Unchanged
        $this->assertEquals('Original Description', $result->description); // Unchanged
        $this->assertTrue($result->completed); // Changed
    }

    public function test_delete_task_removes_from_database()
    {
        $task = Task::factory()->create();

        $service = new TaskService;
        $service->delete($task);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
