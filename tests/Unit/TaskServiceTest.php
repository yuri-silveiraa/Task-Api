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
        // Arrange
        Task::factory()->count(3)->create();

        // Act
        $service = new TaskService;
        $result = $service->list();

        // Assert
        $this->assertCount(3, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_create_task_with_valid_data()
    {
        // Arrange
        $user = \App\Models\User::factory()->create();
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'completed' => false,
        ];

        // Act
        $service = new TaskService;
        $result = $service->create($user->id, $taskData);

        // Assert
        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals('Test Task', $result->title);
        $this->assertEquals('Test Description', $result->description);
        $this->assertFalse($result->completed);
    }

    public function test_create_task_with_partial_data()
    {
        // Arrange
        $user = \App\Models\User::factory()->create();
        $taskData = [
            'title' => 'Partial Task',
        ];

        // Act
        $service = new TaskService;
        $result = $service->create($user->id, $taskData);

        // Assert
        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals('Partial Task', $result->title);
        $this->assertNull($result->description);
        $this->assertNull($result->completed); // Default value
    }

    public function test_update_task_with_valid_data()
    {
        // Arrange
        $task = Task::factory()->create(['title' => 'Original Title']);
        $updateData = [
            'title' => 'Updated Task',
            'completed' => true,
        ];

        // Act
        $service = new TaskService;
        $result = $service->update($task, $updateData);

        // Assert
        $this->assertSame($task, $result);
        $this->assertEquals('Updated Task', $result->title);
        $this->assertTrue($result->completed);
    }

    public function test_update_task_with_partial_data()
    {
        // Arrange
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'description' => 'Original Description',
            'completed' => false,
        ]);
        $updateData = [
            'completed' => true,
        ];

        // Act
        $service = new TaskService;
        $result = $service->update($task, $updateData);

        // Assert
        $this->assertSame($task, $result);
        $this->assertEquals('Original Title', $result->title); // Unchanged
        $this->assertEquals('Original Description', $result->description); // Unchanged
        $this->assertTrue($result->completed); // Changed
    }

    public function test_delete_task_removes_from_database()
    {
        // Arrange
        $task = Task::factory()->create();

        // Act
        $service = new TaskService;
        $service->delete($task);

        // Assert
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
