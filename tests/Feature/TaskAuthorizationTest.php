<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    // ==================== MEMBER ROLE TESTS ====================

    public function test_member_cannot_create_task()
    {
        $user = User::factory()->member()->create();

        $response = $this->withJwtAuth($user)
            ->postJson('/api/tasks', [
                'title' => 'Test Task',
            ]);

        $response->assertStatus(403); // Forbidden
    }

    public function test_member_cannot_update_own_task()
    {
        $user = User::factory()->member()->create();
        $task = Task::factory()->forUser($user)->create();

        $response = $this->withJwtAuth($user)
            ->patchJson("/api/tasks/{$task->id}", [
                'title' => 'Updated Title',
            ]);

        $response->assertStatus(403);
    }

    public function test_member_cannot_update_other_user_task()
    {
        $user = User::factory()->member()->create();
        $otherUser = User::factory()->member()->create();
        $task = Task::factory()->forUser($otherUser)->create();

        $response = $this->withJwtAuth($user)
            ->patchJson("/api/tasks/{$task->id}", [
                'title' => 'Updated Title',
            ]);

        $response->assertStatus(403);
    }

    public function test_member_cannot_delete_task()
    {
        $user = User::factory()->member()->create();
        $task = Task::factory()->forUser($user)->create();

        $response = $this->withJwtAuth($user)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    // ==================== MANAGER ROLE TESTS ====================

    public function test_manager_can_create_task()
    {
        $manager = User::factory()->manager()->create();

        $response = $this->withJwtAuth($manager)
            ->postJson('/api/tasks', [
                'title' => 'Manager Task',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Manager Task',
            'user_id' => $manager->id,
        ]);
    }

    public function test_manager_can_update_own_task()
    {
        $manager = User::factory()->manager()->create();
        $task = Task::factory()->forUser($manager)->create();

        $response = $this->withJwtAuth($manager)
            ->patchJson("/api/tasks/{$task->id}", [
                'title' => 'Updated Manager Task',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('Updated Manager Task', $response->json('title'));
    }

    public function test_manager_cannot_update_other_user_task()
    {
        $manager = User::factory()->manager()->create();
        $otherUser = User::factory()->member()->create();
        $task = Task::factory()->forUser($otherUser)->create();

        $response = $this->withJwtAuth($manager)
            ->patchJson("/api/tasks/{$task->id}", [
                'title' => 'Hacked Task',
            ]);

        $response->assertStatus(403);
    }

    public function test_manager_cannot_delete_task()
    {
        $manager = User::factory()->manager()->create();
        $task = Task::factory()->forUser($manager)->create();

        $response = $this->withJwtAuth($manager)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    // ==================== ADMIN ROLE TESTS ====================

    public function test_admin_can_create_task()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->withJwtAuth($admin)
            ->postJson('/api/tasks', [
                'title' => 'Admin Task',
            ]);

        $response->assertStatus(201);
    }

    public function test_admin_can_update_any_task()
    {
        $admin = User::factory()->admin()->create();
        $otherUser = User::factory()->member()->create();
        $task = Task::factory()->forUser($otherUser)->create();

        $response = $this->withJwtAuth($admin)
            ->patchJson("/api/tasks/{$task->id}", [
                'title' => 'Admin Modified',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('Admin Modified', $response->json('title'));
    }

    public function test_admin_can_delete_any_task()
    {
        $admin = User::factory()->admin()->create();
        $otherUser = User::factory()->member()->create();
        $task = Task::factory()->forUser($otherUser)->create();

        $response = $this->withJwtAuth($admin)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    // ==================== VALIDATION TESTS ====================

    public function test_task_creation_requires_title_min_3_chars()
    {
        $manager = User::factory()->manager()->create();

        $response = $this->withJwtAuth($manager)
            ->postJson('/api/tasks', [
                'title' => 'ab', // Muito curto
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }

    public function test_task_creation_accepts_valid_data()
    {
        $manager = User::factory()->manager()->create();

        $response = $this->withJwtAuth($manager)
            ->postJson('/api/tasks', [
                'title' => 'Valid Task Title',
                'description' => 'Valid description',
                'completed' => true,
            ]);

        $response->assertStatus(201);

        // Verifica se a task foi criada com os dados corretos
        $this->assertDatabaseHas('tasks', [
            'title' => 'Valid Task Title',
            'description' => 'Valid description',
            'user_id' => $manager->id,
        ]);

        // Verifica o response JSON para os campos essenciais
        $response->assertJsonFragment([
            'title' => 'Valid Task Title',
            'description' => 'Valid description',
            'user_id' => $manager->id,
        ]);
    }

    public function test_task_update_accepts_partial_data()
    {
        $manager = User::factory()->manager()->create();
        $task = Task::factory()->forUser($manager)->create([
            'title' => 'Original Title',
            'completed' => false,
        ]);

        $response = $this->withJwtAuth($manager)
            ->patchJson("/api/tasks/{$task->id}", [
                'completed' => true,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'title' => 'Original Title', // Não mudou
            'completed' => true,           // Mudou
        ]);
    }
}
