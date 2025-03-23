<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_create_project(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/projects', [
                'name' => 'New Project',
                'description' => 'Project Description'
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('projects', [
            'name' => 'New Project',
            'description' => 'Project Description',
            'user_id' => $this->user->id
        ]);
    }

    public function test_user_can_update_project(): void
    {
        $project = Project::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->patch("/projects/{$project->id}", [
                'name' => 'Updated Project',
                'description' => 'Updated Description'
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project',
            'description' => 'Updated Description'
        ]);
    }

    public function test_user_can_delete_project(): void
    {
        $project = Project::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->delete("/projects/{$project->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('projects', [
            'id' => $project->id
        ]);
    }

    public function test_user_can_view_their_projects(): void
    {
        Project::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get('/projects');

        $response->assertStatus(200);
        $response->assertViewHas('projects');
    }

    public function test_user_cannot_access_other_users_projects(): void
    {
        $otherUser = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user)
            ->get("/projects/{$project->id}");

        $response->assertStatus(403);
    }
} 