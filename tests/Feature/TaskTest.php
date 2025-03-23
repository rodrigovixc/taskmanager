<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->project = Project::create([
            'name' => 'Test Project',
            'description' => 'Test Description',
            'status' => 'in_progress',
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(30),
        ]);
    }

    public function test_tasks_page_requires_authentication(): void
    {
        $response = $this->get('/tasks');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_tasks_page(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/tasks');
            
        $response->assertOk();
        $response->assertSeeLivewire('task-manager');
    }

    public function test_user_can_create_a_task(): void
    {
        Livewire::actingAs($this->user)
            ->test('task-manager')
            ->set('title', 'New Task')
            ->set('description', 'Task Description')
            ->set('status', 'todo')
            ->set('priority', 'high')
            ->set('project_id', $this->project->id)
            ->set('due_date', now()->addDays(7))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task',
            'description' => 'Task Description',
            'status' => 'todo',
            'priority' => 'high',
            'project_id' => $this->project->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_task_requires_a_title(): void
    {
        Livewire::actingAs($this->user)
            ->test('task-manager')
            ->set('description', 'Task Description')
            ->set('status', 'todo')
            ->set('priority', 'high')
            ->call('save')
            ->assertHasErrors(['title' => 'required']);
    }

    public function test_user_can_update_a_task(): void
    {
        $task = Task::create([
            'title' => 'Original Task',
            'description' => 'Original Description',
            'status' => 'todo',
            'priority' => 'medium',
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'due_date' => now()->addDays(7),
        ]);

        Livewire::actingAs($this->user)
            ->test('task-manager')
            ->call('editTask', $task->id)
            ->set('title', 'Updated Task')
            ->set('description', 'Updated Description')
            ->set('status', 'in_progress')
            ->set('priority', 'high')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'status' => 'in_progress',
            'priority' => 'high',
        ]);
    }

    public function test_user_can_delete_a_task(): void
    {
        $task = Task::create([
            'title' => 'Task to Delete',
            'description' => 'Will be deleted',
            'status' => 'todo',
            'priority' => 'low',
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'due_date' => now()->addDays(7),
        ]);

        Livewire::actingAs($this->user)
            ->test('task-manager')
            ->call('deleteTask', $task->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_can_drag_and_drop_tasks_between_statuses(): void
    {
        $task = Task::create([
            'title' => 'Draggable Task',
            'description' => 'Can be moved',
            'status' => 'todo',
            'priority' => 'medium',
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'due_date' => now()->addDays(7),
        ]);

        Livewire::actingAs($this->user)
            ->test('task-manager')
            ->call('updateTaskStatus', $task->id, 'in_progress')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_user_can_only_see_their_own_tasks(): void
    {
        $otherUser = User::factory()->create();
        
        $userTask = Task::create([
            'title' => 'My Task',
            'description' => 'My Description',
            'status' => 'todo',
            'priority' => 'high',
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'due_date' => now()->addDays(7),
        ]);

        $otherTask = Task::create([
            'title' => 'Other Task',
            'description' => 'Other Description',
            'status' => 'todo',
            'priority' => 'high',
            'user_id' => $otherUser->id,
            'project_id' => $this->project->id,
            'due_date' => now()->addDays(7),
        ]);

        $response = $this->actingAs($this->user)
            ->get('/tasks');

        $response->assertSee('My Task');
        $response->assertDontSee('Other Task');
    }
} 