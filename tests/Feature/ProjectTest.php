<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_project_page_requires_authentication(): void
    {
        $response = $this->get('/projects');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_projects_page(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/projects');
            
        $response->assertOk();
        $response->assertSeeLivewire('project-list');
    }

    public function test_user_can_create_a_project(): void
    {
        Livewire::actingAs($this->user)
            ->test('project-list')
            ->set('name', 'New Project')
            ->set('description', 'Project Description')
            ->set('status', 'todo')
            ->set('due_date', now()->addDays(30))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects', [
            'name' => 'New Project',
            'description' => 'Project Description',
            'status' => 'todo',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_project_requires_a_name(): void
    {
        Livewire::actingAs($this->user)
            ->test('project-list')
            ->set('description', 'Project Description')
            ->set('status', 'todo')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_user_can_update_a_project(): void
    {
        $project = Project::create([
            'name' => 'Original Project',
            'description' => 'Original Description',
            'status' => 'todo',
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(30),
        ]);

        Livewire::actingAs($this->user)
            ->test('project-list')
            ->call('edit', $project->id)
            ->set('name', 'Updated Project')
            ->set('description', 'Updated Description')
            ->set('status', 'in_progress')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project',
            'description' => 'Updated Description',
            'status' => 'in_progress',
        ]);
    }

    public function test_user_can_delete_a_project(): void
    {
        $project = Project::create([
            'name' => 'Project to Delete',
            'description' => 'Will be deleted',
            'status' => 'todo',
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(30),
        ]);

        Livewire::actingAs($this->user)
            ->test('project-list')
            ->call('delete', $project->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }

    public function test_user_can_only_see_their_own_projects(): void
    {
        $otherUser = User::factory()->create();
        
        $userProject = Project::create([
            'name' => 'My Project',
            'description' => 'My Description',
            'status' => 'todo',
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(30),
        ]);

        $otherProject = Project::create([
            'name' => 'Other Project',
            'description' => 'Other Description',
            'status' => 'todo',
            'user_id' => $otherUser->id,
            'due_date' => now()->addDays(30),
        ]);

        Livewire::actingAs($this->user)
            ->test('project-list')
            ->assertSee('My Project')
            ->assertDontSee('Other Project');
    }
} 