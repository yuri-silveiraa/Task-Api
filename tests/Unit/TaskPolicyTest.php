<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use App\Policies\TaskPolicy;
use Mockery;
use Tests\TestCase;

class TaskPolicyTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // ==================== BEFORE METHOD TESTS ====================

    public function test_before_returns_true_for_admin()
    {
        // Arrange
        $admin = Mockery::mock(User::class);
        $admin->shouldReceive('isAdmin')
            ->once()
            ->andReturn(true);

        $policy = new TaskPolicy;

        // Act
        $result = $policy->before($admin);

        // Assert
        $this->assertTrue($result);
    }

    public function test_before_returns_null_for_non_admin()
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->shouldReceive('isAdmin')
            ->once()
            ->andReturn(false);

        $policy = new TaskPolicy;

        // Act
        $result = $policy->before($user);

        // Assert
        $this->assertNull($result);
    }

    // ==================== VIEWANY METHOD TESTS ====================

    public function test_view_any_returns_true_for_any_authenticated_user()
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $policy = new TaskPolicy;

        // Act
        $result = $policy->viewAny($user);

        // Assert
        $this->assertTrue($result);
    }

    // ==================== CREATE METHOD TESTS ====================

    public function test_create_returns_true_for_manager()
    {
        // Arrange
        $manager = Mockery::mock(User::class);
        $manager->shouldReceive('isManager')
            ->once()
            ->andReturn(true);

        $policy = new TaskPolicy;

        // Act
        $result = $policy->create($manager);

        // Assert
        $this->assertTrue($result);
    }

    public function test_create_returns_false_for_member()
    {
        // Arrange
        $member = Mockery::mock(User::class);
        $member->shouldReceive('isManager')
            ->once()
            ->andReturn(false);

        $policy = new TaskPolicy;

        // Act
        $result = $policy->create($member);

        // Assert
        $this->assertFalse($result);
    }

    // ==================== UPDATE METHOD TESTS ====================

    public function test_update_returns_true_for_manager_owning_task()
    {
        // Arrange
        $manager = Mockery::mock(User::class);
        $manager->shouldReceive('isManager')
            ->once()
            ->andReturn(true);
        $manager->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(123);

        $task = new Task;
        $task->user_id = 123;

        $policy = new TaskPolicy;

        // Act
        $result = $policy->update($manager, $task);

        // Assert
        $this->assertTrue($result);
    }

    public function test_update_returns_false_for_manager_not_owning_task()
    {
        // Arrange
        $manager = Mockery::mock(User::class);
        $manager->shouldReceive('isManager')
            ->once()
            ->andReturn(true);
        $manager->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(123);

        $task = new Task;
        $task->user_id = 456;

        $policy = new TaskPolicy;

        // Act
        $result = $policy->update($manager, $task);

        // Assert
        $this->assertFalse($result);
    }

    public function test_update_returns_false_for_member()
    {
        // Arrange
        $member = Mockery::mock(User::class);
        $member->shouldReceive('isManager')
            ->once()
            ->andReturn(false);

        $task = new Task;

        $policy = new TaskPolicy;

        // Act
        $result = $policy->update($member, $task);

        // Assert
        $this->assertFalse($result);
    }

    // ==================== DELETE METHOD TESTS ====================

    public function test_delete_returns_false_for_manager()
    {
        // Arrange
        $manager = Mockery::mock(User::class);
        $task = Mockery::mock(Task::class);

        $policy = new TaskPolicy;

        // Act
        $result = $policy->delete($manager, $task);

        // Assert
        $this->assertFalse($result);
    }

    public function test_delete_returns_false_for_member()
    {
        // Arrange
        $member = Mockery::mock(User::class);
        $task = Mockery::mock(Task::class);

        $policy = new TaskPolicy;

        // Act
        $result = $policy->delete($member, $task);

        // Assert
        $this->assertFalse($result);
    }

    public function test_delete_returns_false_for_admin_without_before()
    {
        // Arrange
        $admin = Mockery::mock(User::class);
        $task = Mockery::mock(Task::class);

        $policy = new TaskPolicy;

        // Act
        $result = $policy->delete($admin, $task);

        // Assert
        $this->assertFalse($result);
        // Note: Admins get true from before() method, tested separately
    }
}
