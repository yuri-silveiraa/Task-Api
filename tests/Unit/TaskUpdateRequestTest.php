<?php

namespace Tests\Unit;

use App\Http\Requests\TaskUpdateRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskUpdateRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorize_returns_true()
    {
        // Act
        $request = new TaskUpdateRequest;
        $result = $request->authorize();

        // Assert
        $this->assertTrue($result);
    }

    public function test_rules_returns_expected_validation_rules()
    {
        // Arrange
        $request = new TaskUpdateRequest;

        // Act
        $rules = $request->rules();

        // Assert
        $expectedRules = [
            'title' => 'sometimes|string|min:3',
            'description' => 'sometimes|nullable|string',
            'completed' => 'sometimes|boolean',
        ];

        $this->assertEquals($expectedRules, $rules);
    }

    public function test_title_when_provided_must_be_minimum_3_characters()
    {
        // Arrange
        $this->actingAs($this->createManager());
        $task = $this->createTaskForUser();

        $data = [
            'title' => 'ab', // Only 2 characters
        ];

        // Act
        $response = $this->patchJson("/api/tasks/{$task->id}", $data);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }

    public function test_title_when_valid_passes_validation()
    {
        // Arrange
        $this->actingAs($this->createManager());
        $task = $this->createTaskForUser();

        $data = [
            'title' => 'Updated valid title',
        ];

        // Act
        $response = $this->patchJson("/api/tasks/{$task->id}", $data);

        // Assert
        $response->assertStatus(200);
    }

    public function test_description_when_provided_must_be_string()
    {
        // Arrange
        $this->actingAs($this->createManager());
        $task = $this->createTaskForUser();

        $data = [
            'description' => 12345, // Not a string
        ];

        // Act
        $response = $this->patchJson("/api/tasks/{$task->id}", $data);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['description']);
    }

    public function test_description_when_nullable_passes_validation()
    {
        // Arrange
        $this->actingAs($this->createManager());
        $task = $this->createTaskForUser();

        $data = [
            'description' => null,
        ];

        // Act
        $response = $this->patchJson("/api/tasks/{$task->id}", $data);

        // Assert
        $response->assertStatus(200);
    }

    public function test_completed_when_provided_must_be_boolean()
    {
        // Arrange
        $this->actingAs($this->createManager());
        $task = $this->createTaskForUser();

        $data = [
            'completed' => 'true', // String instead of boolean
        ];

        // Act
        $response = $this->patchJson("/api/tasks/{$task->id}", $data);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['completed']);
    }

    public function test_completed_when_true_passes_validation()
    {
        // Arrange
        $this->actingAs($this->createManager());
        $task = $this->createTaskForUser();

        $data = [
            'completed' => true,
        ];

        // Act
        $response = $this->patchJson("/api/tasks/{$task->id}", $data);

        // Assert
        $response->assertStatus(200);
    }

    public function test_completed_when_false_passes_validation()
    {
        // Arrange
        $this->actingAs($this->createManager());
        $task = $this->createTaskForUser();

        $data = [
            'completed' => false,
        ];

        // Act
        $response = $this->patchJson("/api/tasks/{$task->id}", $data);

        // Assert
        $response->assertStatus(200);
    }

    public function test_partial_update_with_single_field_passes_validation()
    {
        // Arrange
        $this->actingAs($this->createManager());
        $task = $this->createTaskForUser();

        $data = [
            'title' => 'Just update title',
        ];

        // Act
        $response = $this->patchJson("/api/tasks/{$task->id}", $data);

        // Assert
        $response->assertStatus(200);
    }

    private function createManager()
    {
        return \App\Models\User::factory()->manager()->create();
    }

    private function createTaskForUser()
    {
        $user = $this->createManager();
        $this->actingAs($user);

        return \App\Models\Task::factory()->create(['user_id' => $user->id]);
    }
}
