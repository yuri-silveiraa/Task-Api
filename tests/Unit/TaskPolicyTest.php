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
        $admin = Mockery::mock(User::class);
        $admin->shouldReceive('isAdmin')
            ->once()
            ->andReturn(true);

        $policy = new TaskPolicy;

        $result = $policy->before($admin);

        $this->assertTrue($result);
    }

    public function test_before_returns_null_for_non_admin()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('isAdmin')
            ->once()
            ->andReturn(false);

        $policy = new TaskPolicy;

        $result = $policy->before($user);

        $this->assertNull($result);
    }

    // ==================== VIEWANY METHOD TESTS ====================

    public function test_view_any_returns_true_for_any_authenticated_user()
    {
        $user = Mockery::mock(User::class);
        $policy = new TaskPolicy;

        $result = $policy->viewAny($user);

        $this->assertTrue($result);
    }

    // ==================== CREATE METHOD TESTS ====================

    public function test_create_returns_true_for_manager()
    {
        $manager = Mockery::mock(User::class);
        $manager->shouldReceive('isManager')
            ->once()
            ->andReturn(true);

        $policy = new TaskPolicy;

        $result = $policy->create($manager);

        $this->assertTrue($result);
    }

    public function test_create_returns_false_for_member()
    {
        $member = Mockery::mock(User::class);
        $member->shouldReceive('isManager')
            ->once()
            ->andReturn(false);

        $policy = new TaskPolicy;

        $result = $policy->create($member);

        $this->assertFalse($result);
    }

    // ==================== UPDATE METHOD TESTS ====================

    public function test_update_returns_true_for_manager_owning_task()
    {
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

        $result = $policy->update($manager, $task);

        $this->assertTrue($result);
    }

    public function test_update_returns_false_for_manager_not_owning_task()
    {
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

        $result = $policy->update($manager, $task);

        $this->assertFalse($result);
    }

    public function test_update_returns_false_for_member()
    {
        $member = Mockery::mock(User::class);
        $member->shouldReceive('isManager')
            ->once()
            ->andReturn(false);

        $task = new Task;

        $policy = new TaskPolicy;

        $result = $policy->update($member, $task);

        $this->assertFalse($result);
    }

    // ==================== DELETE METHOD TESTS ====================

    public function test_delete_returns_false_for_manager()
    {
        $manager = Mockery::mock(User::class);
        $task = Mockery::mock(Task::class);

        $policy = new TaskPolicy;

        $result = $policy->delete($manager, $task);

        $this->assertFalse($result);
    }

    public function test_delete_returns_false_for_member()
    {
        $member = Mockery::mock(User::class);
        $task = Mockery::mock(Task::class);

        $policy = new TaskPolicy;

        $result = $policy->delete($member, $task);

        $this->assertFalse($result);
    }

    public function test_delete_returns_false_for_admin_without_before()
    {
        // Arrange
        $admin = Mockery::mock(User::class);
        $task = Mockery::mock(Task::class);

        $policy = new TaskPolicy;

        $result = $policy->delete($admin, $task);

        $this->assertFalse($result);
    }
}
