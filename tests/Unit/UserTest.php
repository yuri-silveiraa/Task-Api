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
        $user = User::factory()->admin()->create();

        $result = $user->isAdmin();

        $this->assertTrue($result);
    }

    public function test_is_admin_returns_false_for_manager_role()
    {
        $user = User::factory()->manager()->create();

        $result = $user->isAdmin();

        $this->assertFalse($result);
    }

    public function test_is_admin_returns_false_for_member_role()
    {
        $user = User::factory()->member()->create();

        $result = $user->isAdmin();

        $this->assertFalse($result);
    }

    public function test_is_manager_returns_true_for_manager_role()
    {
        $user = User::factory()->manager()->create();

        $result = $user->isManager();

        $this->assertTrue($result);
    }

    public function test_is_manager_returns_false_for_admin_role()
    {
        $user = User::factory()->admin()->create();

        $result = $user->isManager();

        $this->assertFalse($result);
    }

    public function test_is_manager_returns_false_for_member_role()
    {
        $user = User::factory()->member()->create();

        $result = $user->isManager();

        $this->assertFalse($result);
    }

    public function test_tasks_relationship_returns_has_many()
    {
        $user = User::factory()->manager()->create();

        $user->tasks()->createMany([
            ['title' => 'Task 1'],
            ['title' => 'Task 2'],
        ]);

        $tasks = $user->tasks;

        $this->assertCount(2, $tasks);
        $this->assertEquals('Task 1', $tasks[0]->title);
        $this->assertEquals('Task 2', $tasks[1]->title);
    }

    public function test_jwt_identifier_returns_key()
    {
        $user = User::factory()->member()->create(['id' => 42]);

        $result = $user->getJWTIdentifier();

        $this->assertEquals(42, $result);
    }

    public function test_jwt_custom_claims_returns_empty_array()
    {
        $user = User::factory()->member()->create();

        $result = $user->getJWTCustomClaims();

        $this->assertEquals([], $result);
    }
}
