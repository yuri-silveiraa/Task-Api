<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes_are_correct()
    {
        $task = new Task;

        $fillable = $task->getFillable();

        $expectedFillable = ['title', 'description', 'completed', 'user_id'];
        $this->assertEquals($expectedFillable, $fillable);
    }

    public function test_casts_are_correct()
    {
        $task = new Task;

        $casts = $task->getCasts();

        $this->assertArrayHasKey('completed', $casts);
        $this->assertEquals('boolean', $casts['completed']);
    }

    public function test_user_relationship_returns_belongs_to()
    {
        $user = User::factory()->manager()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $relatedUser = $task->user;

        $this->assertInstanceOf(User::class, $relatedUser);
        $this->assertEquals($user->id, $relatedUser->id);
        $this->assertEquals($user->name, $relatedUser->name);
    }

    public function test_task_can_be_created_with_all_attributes()
    {
        $user = User::factory()->manager()->create();

        $taskData = [
            'title' => 'Test Task Title',
            'description' => 'Test Task Description',
            'completed' => true,
            'user_id' => $user->id,
        ];

        $task = Task::create($taskData);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Test Task Title', $task->title);
        $this->assertEquals('Test Task Description', $task->description);
        $this->assertTrue($task->completed);
        $this->assertEquals($user->id, $task->user_id);
    }

    public function test_task_can_be_created_with_partial_attributes()
    {
        $user = User::factory()->manager()->create();

        $taskData = [
            'title' => 'Partial Task Title',
            'user_id' => $user->id,
        ];

        $task = Task::create($taskData);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Partial Task Title', $task->title);
        $this->assertNull($task->description);
        $this->assertNull($task->completed);
        $this->assertEquals($user->id, $task->user_id);
    }

    public function test_has_factory_trait()
    {
        $task = Task::factory()->create();

        $this->assertInstanceOf(Task::class, $task);
        $this->assertNotNull($task->id);
    }
}
