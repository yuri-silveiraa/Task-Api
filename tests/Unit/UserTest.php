<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_admin_returns_true_for_admin_role()
    {
        // Arrange
        $user = User::factory()->admin()->create();

        // Act
        $result = $user->isAdmin();

        // Assert
        $this->assertTrue($result);
    }

    public function test_is_admin_returns_false_for_manager_role()
    {
        // Arrange
        $user = User::factory()->manager()->create();

        // Act
        $result = $user->isAdmin();

        // Assert
        $this->assertFalse($result);
    }

    public function test_is_admin_returns_false_for_member_role()
    {
        // Arrange
        $user = User::factory()->member()->create();

        // Act
        $result = $user->isAdmin();

        // Assert
        $this->assertFalse($result);
    }

    public function test_is_manager_returns_true_for_manager_role()
    {
        // Arrange
        $user = User::factory()->manager()->create();

        // Act
        $result = $user->isManager();

        // Assert
        $this->assertTrue($result);
    }

    public function test_is_manager_returns_false_for_admin_role()
    {
        // Arrange
        $user = User::factory()->admin()->create();

        // Act
        $result = $user->isManager();

        // Assert
        $this->assertFalse($result);
    }

    public function test_is_manager_returns_false_for_member_role()
    {
        // Arrange
        $user = User::factory()->member()->create();

        // Act
        $result = $user->isManager();

        // Assert
        $this->assertFalse($result);
    }

    public function test_tasks_relationship_returns_has_many()
    {
        // Arrange
        $user = User::factory()->manager()->create();

        // Create tasks for this user
        $user->tasks()->createMany([
            ['title' => 'Task 1'],
            ['title' => 'Task 2'],
        ]);

        // Act
        $tasks = $user->tasks;

        // Assert
        $this->assertCount(2, $tasks);
        $this->assertEquals('Task 1', $tasks[0]->title);
        $this->assertEquals('Task 2', $tasks[1]->title);
    }

    public function test_jwt_identifier_returns_key()
    {
        // Arrange
        $user = User::factory()->member()->create(['id' => 42]);

        // Act
        $result = $user->getJWTIdentifier();

        // Assert
        $this->assertEquals(42, $result);
    }

    public function test_jwt_custom_claims_returns_empty_array()
    {
        // Arrange
        $user = User::factory()->member()->create();

        // Act
        $result = $user->getJWTCustomClaims();

        // Assert
        $this->assertEquals([], $result);
    }
}
